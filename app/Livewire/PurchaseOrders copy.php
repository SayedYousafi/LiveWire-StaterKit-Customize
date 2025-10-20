<?php
namespace App\Livewire;

use App\Models\po;
use App\Models\PurchaseOrder;
use App\Services\OrderItemService;
use Livewire\Component;

class PurchaseOrders extends Component
{
    public $supplierOrderId;
    public $orderedItems;
    public $model = [];
    public $supplier_id, $item_id, $qty, $price;
    public $desc, $comment1, $comment2, $comment3, $comment4, $comment5, $comment6;
    public $editor    = null;
    public $viewingPo = null;

    protected OrderItemService $orderItemService;

    public function boot(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function render()
    {
        if (! $this->editor) {
            $this->orderedItems = $this->orderItemService->getItemOrders($this->supplierOrderId)
                ->where('supplier_items.is_po', 'Yes')
                ->get();
        }
        // If editing, $orderedItems is already set in the edit method, so we skip overriding it here

        $supplier_id = $this->orderedItems->first()->SUPPID ?? null;
        $pos         = $supplier_id ? po::where('supplier_id', $supplier_id)->latest()->get() : collect();

        return view('livewire.purchase-orders')->with([
            'orderedItems' => $this->orderedItems,
            'pos'          => $pos,
        ]);
    }

    public function save()
    {
        $this->validate([
            'desc'     => 'nullable|string|max:255',
            'comment1' => 'nullable|string|max:255',
            'comment2' => 'nullable|string|max:255',
            'comment3' => 'nullable|string|max:255',
            'comment4' => 'nullable|string|max:255',
            'comment5' => 'nullable|string|max:255',
            'comment6' => 'nullable|string|max:255',
            'model.*'  => 'nullable|string|max:255',
        ]);

        $supp              = $this->orderedItems->first();
        $this->supplier_id = $supp->SUPPID;

        $po = po::updateOrCreate(['id' => $this->editor], [
            'supplier_id' => $this->supplier_id,
            'desc'        => $this->desc,
            'comment1'    => $this->comment1,
            'comment2'    => $this->comment2,
            'comment3'    => $this->comment3,
            'comment4'    => $this->comment4,
            'comment5'    => $this->comment5,
            'comment6'    => $this->comment6,
        ]);

        foreach ($this->orderedItems as $item) {
            PurchaseOrder::updateOrCreate(
                [
                    'po_id'   => $po->id,
                    'item_id' => $item->ID,
                ],
                [
                    'po_id'   => $po->id,
                    'item_id' => $item->ID,
                    'qty'     => $item->qty,
                    'price'   => $item->price_rmb,
                    'model'   => $this->model[$item->ID] ?? null,
                ]
            );
        }

        $this->model    = [];
        $this->desc     = '';
        $this->comment1 = '';
        $this->comment2 = '';
        $this->comment3 = '';
        $this->comment4 = '';
        $this->comment5 = '';
        $this->comment6 = '';

        $isUpdate     = $this->editor ? true : false;
        $this->editor = null;

        // Reset orderedItems to the default for this supplierOrderId
        $this->orderedItems = $this->orderItemService->getItemOrders($this->supplierOrderId)
            ->where('supplier_items.is_po', 'Yes')
            ->get();

        session()->flash('success', $isUpdate ? 'Purchase order updated successfully!' : 'Purchase orders created successfully!');
    }

    public function edit($id)
    {
        $this->editor = $id;
        $po           = po::with(['purchaseOrders.item', 'supplier'])->findOrFail($id);

        // Populate po fields
        $this->desc     = $po->desc;
        $this->comment1 = $po->comment1;
        $this->comment2 = $po->comment2;
        $this->comment3 = $po->comment3;
        $this->comment4 = $po->comment4;
        $this->comment5 = $po->comment5;
        $this->comment6 = $po->comment6;

        // Populate model array for each PurchaseOrder
        $this->model = [];
        foreach ($po->purchaseOrders as $purchaseOrder) {
            $this->model[$purchaseOrder->item_id] = $purchaseOrder->model;
        }

        // Load orderedItems from the po's PurchaseOrders
        $this->orderedItems = $po->purchaseOrders->map(function ($purchaseOrder) use ($po) {
            return (object) [
                'SUPPID'       => $po->supplier_id,
                'supplierName' => $po->supplier ? ($po->supplier->name ?? 'N/A') : 'N/A',
                'ID'           => $purchaseOrder->item_id,
                'item_name'    => $purchaseOrder->item ? ($purchaseOrder->item->item_name ?? 'N/A') : 'N/A',
                'qty'          => $purchaseOrder->qty,
                'price_rmb'    => $purchaseOrder->price,
            ];
        });
    }

    public function view($id)
    {
        $this->viewingPo = po::with([
            'purchaseOrders.item.attachments',
            'purchaseOrders.item.itemQualities',
            'supplier',
        ])->findOrFail($id);
    }

    public function closeView()
    {
        $this->viewingPo = null;
    }

    public function download($id)
    {
        $po = po::with([
            'purchaseOrders.item.attachments',   // PDFs (kept)
            'purchaseOrders.item.itemQualities', // images (kept only for your Blade use)
            'supplier',
        ])->findOrFail($id);

        $tmpDir = storage_path('app/temp_po/' . $po->id);
        if (! \Illuminate\Support\Facades\File::exists($tmpDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($tmpDir, 0775, true);
        }

        // 1) Base PO via DomPDF
        $basePdfPath = $tmpDir . '/po_base.pdf';
        $pdf         = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.purchase-order-pic-pdf', ['po' => $po])
            ->setPaper('a4', 'portrait');

        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);

        \Illuminate\Support\Facades\File::put($basePdfPath, $pdf->output());
        \Illuminate\Support\Facades\Log::info("PO merge: base created -> {$basePdfPath}");

        $sources = [$basePdfPath];

        // 2) Attachments (PDFs only)
        $rawAttachments = $po->purchaseOrders->pluck('item.attachments')->flatten();
        \Illuminate\Support\Facades\Log::info('PO merge: found attachments count = ' . $rawAttachments->count());

        $attachments = $rawAttachments->filter(function ($a) {
            $ext = strtolower(pathinfo($a->path ?? '', PATHINFO_EXTENSION));
            return $ext === 'pdf';
        });

        foreach ($attachments as $a) {
            $abs = $this->resolvePublicFile($a->path, ['pdfs', 'storage/pdfs']);
            if ($abs) {
                $sources[] = $abs;
                \Illuminate\Support\Facades\Log::info("PO merge: + attachment PDF -> {$abs}");
            } else {
                \Illuminate\Support\Facades\Log::warning("PO merge: attachment not found -> '{$a->path}'");
            }
        }

        // 3) Merge with FPDI
        $finalPath = $tmpDir . '/PO_' . $po->id . '.pdf';
        $fpdi      = new \setasign\Fpdi\Fpdi();

        $fpdi->SetAutoPageBreak(false); // ✅ prevents automatic new pages
        $fpdi->SetMargins(0, 0, 0);

        foreach ($sources as $file) {
            if (! \Illuminate\Support\Facades\File::exists($file)) {
                \Illuminate\Support\Facades\Log::warning("PO merge: source missing at merge time -> {$file}");
                continue;
            }

            $pageCount = $this->safeSetSourceFile($fpdi, $file);
            if ($pageCount < 1) {
                \Illuminate\Support\Facades\Log::warning("PO merge: no pages in source -> {$file}");
                continue;
            }

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                try {
                    $tplId       = $fpdi->importPage($pageNo, \setasign\Fpdi\PdfReader\PageBoundaries::MEDIA_BOX);
                    $size        = $fpdi->getTemplateSize($tplId);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                    $fpdi->useTemplate($tplId);

                    // --- Simple page number: 1, 2, 3 ... centered at bottom ---
                    $fpdi->SetFont('Helvetica', '', 10);
                    $fpdi->SetTextColor(100, 100, 100);

                    $number = (string) $fpdi->PageNo();

                    // absolute positioning (no page break checks)
                    $pageW = $fpdi->GetPageWidth();
                    $pageH = $fpdi->GetPageHeight();
                    $y     = $pageH - 8; // 8mm from bottom

                    // center horizontally using string width
                    $w = $fpdi->GetStringWidth($number);
                    $x = ($pageW - $w) / 2;

                    $fpdi->Text($x, $y, $number);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("PO merge: failed importing page {$pageNo} of {$file}: " . $e->getMessage());
                }
            }
        }

        $fpdi->Output($finalPath, 'F');
        \Illuminate\Support\Facades\Log::info("PO merge: final at -> {$finalPath}");

        return response()->download($finalPath, 'PO_' . $po->id . '.pdf')->deleteFileAfterSend(true);
    }

    private function resolvePublicFile(?string $relativeOrAbs, array $fallbacks = []): ?string
    {
        if (! $relativeOrAbs) {
            return null;
        }

        $candidates = [];

        if (is_string($relativeOrAbs) && \Illuminate\Support\Facades\File::exists($relativeOrAbs)) {
            $candidates[] = $relativeOrAbs;
        }

        $candidates[] = public_path($relativeOrAbs);
        $candidates[] = storage_path('app/public/' . ltrim($relativeOrAbs, '/'));

        foreach ($fallbacks as $fb) {
            $candidates[] = public_path(rtrim($fb, '/') . '/' . ltrim($relativeOrAbs, '/'));
            $candidates[] = storage_path('app/public/' . rtrim($fb, '/') . '/' . ltrim($relativeOrAbs, '/'));
        }

        foreach ($candidates as $c) {
            if ($c && \Illuminate\Support\Facades\File::exists($c)) {
                return $c;
            }
        }
        return null;
    }

    private function safeSetSourceFile(\setasign\Fpdi\Fpdi $fpdi, string $file): int
    {
        try {
            return $fpdi->setSourceFile($file);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("FPDI: cannot read PDF '{$file}': " . $e->getMessage());
            return 0;
        }
    }

}
