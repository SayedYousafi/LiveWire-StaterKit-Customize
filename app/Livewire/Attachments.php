<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Attachment;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\AttachmentItem;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class Attachments extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $selectedFile;
    #[Reactive]
    public $itemId;
    public $search = '';
    public $pdf;

    public function save()
    {
        // Validate the PDF file
        $this->validate([
            'pdf' => 'required|mimes:pdf|max:1024', // 1MB
        ]);

        // Store in public/storage/pdfs folder
        $filePath = $this->pdf->storeAs(
            'pdfs',
            $this->pdf->getClientOriginalName(),
            'public'
        );

        // Optionally: store in DB
        $attachmentId = DB::table('attachments')->insertGetId([
            'filename'   => $this->pdf->getClientOriginalName(),
            'path'       => $filePath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('attachment_item')->insert([
            'attachment_id' => $attachmentId,
            'item_id'       => $this->itemId,
        ]);

        // Reset the file
        $this->reset('pdf');

        // Show success
        session()->flash('success', 'PDF file uploaded successfully!');

        // Clear the cache
        Cache::forget('uploaded_pdfs');

        return $filePath;
    }

    public function loadFiles()
    {
        $directoryPath = public_path('storage/pdfs');

        // Cache list for 60 min
        $files = Cache::remember('uploaded_pdfs', 60, function () use ($directoryPath) {
            if (File::exists($directoryPath)) {
                return collect(File::files($directoryPath))
                    ->sortByDesc(fn($file) => $file->getMTime())
                    ->map(function ($file) {
                        return [
                            'name'  => $file->getFilename(),
                            'path'  => asset('storage/pdfs/' . $file->getFilename()),
                            'mtime' => $file->getMTime(),
                        ];
                    })
                    ->toArray();
            }
            return [];
        });

        // Search filter
        if ($this->search) {
            $searchTerm = strtolower($this->search);
            $files      = collect($files)->filter(function ($file) use ($searchTerm) {
                return str_contains(strtolower($file['name']), $searchTerm);
            })->values()->toArray();
        }

        // Pagination
        $perPage      = 50;
        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $filesForPage = collect($files)->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $filesForPage,
            count($files),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function resetModal()
    {
        $this->reset(['selectedFile', 'search', 'pdf']);
    }
    public function savePdf($pdfName)
    {
        // Find attachment record for this filename
        $attachment = \DB::table('attachments')->where('filename', $pdfName)->first();

        if (! $attachment) {
            session()->flash('error', 'Attachment not found.');
            return;
        }

        // Check if already attached to this item
        $alreadyAttached = DB::table('attachment_item')
            ->where('attachment_id', $attachment->id)
            ->where('item_id', $this->itemId)
            ->exists();

        if ($alreadyAttached) {
            session()->flash('warning', 'This PDF is already attached to the item.');
            return;
        }

        // Insert into pivot table
        DB::table('attachment_item')->insert([
            'attachment_id' => $attachment->id,
            'item_id'       => $this->itemId,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        session()->flash('success', 'PDF file attached successfully!');

        // Force a refresh on the list if you're showing attachments elsewhere
        $this->dispatch('pdf-attached');
    }

    public function download($filePath)
    {
        //dd($filePath);
        return response()->download(storage_path("app\\public\\{$filePath}"));
    }
    public function render()
    {
        $attachments = DB::table('attachments')
            ->join('attachment_item', 'attachments.id', '=', 'attachment_item.attachment_id')
            ->where('attachment_item.item_id', $this->itemId)
            ->get(['attachments.id','attachments.filename', 'attachments.path']);
        $files = $this->loadFiles();

        return view('livewire.attachments', compact('files','attachments'));
    }

    public function deattach($id)
    {
        $deattached = Attachment::findOrFail($id);
        $deattached->delete();
        $deattachedItem = AttachmentItem::where('attachment_id',$id)->where('item_id', $this->itemId);
        $deattachedItem->delete();
        session()->flash('success', 'PDF file De attached successfully for this item!');
    }
}
