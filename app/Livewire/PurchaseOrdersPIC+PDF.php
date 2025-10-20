<?php
namespace App\Livewire;

use App\Models\po;
use App\Models\PurchaseOrder;
use App\Services\OrderItemService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PageBoundaries;

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
            'purchaseOrders.item.attachments',   // PDFs
            'purchaseOrders.item.itemQualities', // images
            'supplier',
        ])->findOrFail($id);

        $tmpDir = storage_path('app/temp_po/' . $po->id);
        if (! File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0775, true);
        }

        // 1) Base PO via DomPDF
        $basePdfPath = $tmpDir . '/po_base.pdf';
        $pdf         = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.purchase-order-pic-pdf', ['po' => $po])
            ->setPaper('a4', 'portrait');

        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);

        File::put($basePdfPath, $pdf->output());
        Log::info("PO merge: base created -> {$basePdfPath}");

        $sources = [$basePdfPath];

        // 2a) Attachments (PDFs)
        $rawAttachments = $po->purchaseOrders->pluck('item.attachments')->flatten();
        Log::info('PO merge: found attachments count = ' . $rawAttachments->count());

        $attachments = $rawAttachments->filter(function ($a) {
            $ext = strtolower(pathinfo($a->path ?? '', PATHINFO_EXTENSION));
            return $ext === 'pdf';
        });

        foreach ($attachments as $a) {
            $abs = $this->resolvePublicFile($a->path, ['pdfs', 'storage/pdfs']);
            if ($abs) {
                $sources[] = $abs;
                Log::info("PO merge: + attachment PDF -> {$abs}");
            } else {
                Log::warning("PO merge: attachment not found -> '{$a->path}'");
            }
        }

        // 2b) Quality pictures (images)
        $rawPictures = $po->purchaseOrders->pluck('item.itemQualities')->flatten();
        Log::info('PO merge: found quality pictures count = ' . $rawPictures->count());

        $idx = 0;
        foreach ($rawPictures as $q) {
            $picRel = $q->picture ?? null;
            // Try typical places: 'pictures/<file>', given string as-is, and storage/public
            $img = $this->resolvePublicFile($picRel, ['pictures', 'storage/pictures']);
            if (! $img) {
                // Also support when DB already stores 'pictures/xxx.png'
                $img = $this->resolvePublicFile('pictures/' . ltrim((string) $picRel, '/'));
            }
            if (! $img) {
                Log::warning("PO merge: picture not found -> '{$picRel}'");
                continue;
            }

            // Ensure FPDF-supported
            $imgSupported = $this->imageToSupported($img);
            if (! $imgSupported) {
                Log::warning("PO merge: picture format unsupported (and convert failed) -> {$img}");
                continue;
            }

            $imgPdf = $tmpDir . '/pic_' . (++$idx) . '.pdf';
            $this->makeSingleImagePdf($imgSupported, $imgPdf);
            if (File::exists($imgPdf)) {
                $sources[] = $imgPdf;
                Log::info("PO merge: + picture page -> {$imgPdf}");
            } else {
                Log::warning("PO merge: failed to create image page -> {$imgPdf}");
            }
        }

        // 3) Merge with FPDI
        $finalPath = $tmpDir . '/PO_' . $po->id . '.pdf';
        $fpdi      = new FPDI();

        foreach ($sources as $file) {
            if (! File::exists($file)) {
                Log::warning("PO merge: source missing at merge time -> {$file}");
                continue;
            }

            $pageCount = $this->safeSetSourceFile($fpdi, $file);
            if ($pageCount < 1) {
                Log::warning("PO merge: no pages in source -> {$file}");
                continue;
            }

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                try {
                    $tplId       = $fpdi->importPage($pageNo, PageBoundaries::MEDIA_BOX);
                    $size        = $fpdi->getTemplateSize($tplId);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                    $fpdi->useTemplate($tplId);
                } catch (\Throwable $e) {
                    Log::error("PO merge: failed importing page {$pageNo} of {$file}: " . $e->getMessage());
                }
            }
        }

        $fpdi->Output($finalPath, 'F');
        Log::info("PO merge: final at -> {$finalPath}");

        return response()->download($finalPath, 'PO_' . $po->id . '.pdf')->deleteFileAfterSend(true);
    }

    private function resolvePublicFile(?string $relativeOrAbs, array $fallbacks = []): ?string
    {
        if (! $relativeOrAbs) {
            return null;
        }

        $candidates = [];

        // If already absolute & exists, use it
        if (is_string($relativeOrAbs) && File::exists($relativeOrAbs)) {
            $candidates[] = $relativeOrAbs;
        }

        // Typical storage/public symlink case
        $candidates[] = public_path($relativeOrAbs);
        $candidates[] = storage_path('app/public/' . ltrim($relativeOrAbs, '/'));

        // Optional fallbacks like 'pdfs/'.$filename or 'pictures/'.$filename
        foreach ($fallbacks as $fb) {
            $candidates[] = public_path(rtrim($fb, '/') . '/' . ltrim($relativeOrAbs, '/'));
            $candidates[] = storage_path('app/public/' . rtrim($fb, '/') . '/' . ltrim($relativeOrAbs, '/'));
        }

        foreach ($candidates as $c) {
            if ($c && File::exists($c)) {
                return $c;
            }

        }
        return null;
    }

    private function imageToSupported(string $imagePath): ?string
    {
        // FPDF supports JPG/JPEG/PNG only. Convert others to PNG via GD if possible.
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $imagePath; // already fine
        }

        // Try to convert using GD
        if (! function_exists('imagecreatefromstring')) {
            Log::warning("GD not available to convert image: {$imagePath}");
            return null;
        }

        $data = @file_get_contents($imagePath);
        if ($data === false) {
            Log::warning("Cannot read image for conversion: {$imagePath}");
            return null;
        }
        $im = @imagecreatefromstring($data);
        if (! $im) {
            Log::warning("GD failed to create image from: {$imagePath}");
            return null;
        }

        $tmpPng = storage_path('app/temp_po/_tmp_' . md5($imagePath . microtime(true)) . '.png');
        if (! File::exists(dirname($tmpPng))) {
            File::makeDirectory(dirname($tmpPng), 0775, true);
        }
        imagepng($im, $tmpPng);
        imagedestroy($im);

        return File::exists($tmpPng) ? $tmpPng : null;
    }

    private function safeSetSourceFile(FPDI $fpdi, string $file): int
    {
        try {
            return $fpdi->setSourceFile($file);
        } catch (\Throwable $e) {
            Log::error("FPDI: cannot read PDF '{$file}': " . $e->getMessage());
            return 0;
        }
    }

    private function makeSingleImagePdf(string $imagePath, string $outPath): void
    {
                                          // A4 in mm: 210 x 297
        $pdf = new Fpdi('P', 'mm', 'A4'); // ✅ use FPDI instead of Fpdf
        $pdf->AddPage();

        [$pxW, $pxH] = getimagesize($imagePath) ?: [0, 0];
        if (! $pxW || ! $pxH) {
            $pdf->Image($imagePath, 10, 10, 190);
        } else {
            $dpi    = 96;
            $imgWmm = ($pxW / $dpi) * 25.4;
            $imgHmm = ($pxH / $dpi) * 25.4;

            $maxW  = 190; // 10mm margins
            $maxH  = 277; // 10mm margins
            $scale = min($maxW / $imgWmm, $maxH / $imgHmm, 1.0);

            $drawW = $imgWmm * $scale;
            $drawH = $imgHmm * $scale;
            $x     = (210 - $drawW) / 2;
            $y     = (297 - $drawH) / 2;

            $pdf->Image($imagePath, $x, $y, $drawW, $drawH);
        }

        $pdf->Output('F', $outPath);
    }

}
