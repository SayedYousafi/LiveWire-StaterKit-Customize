<?php
namespace App\Livewire;

use App\Models\Item;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ImageSelector extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $selectedFile;

    public $itemId;

    public $search = '';

    public $photo;

    public function save()
    {
        // Validate the file (Optional, depending on your use case)
        $this->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Example validation rule
        ]);

        // Store the photo directly in the public storage directory (public/storage)
        $filePath = $this->photo->storeAs('', $this->photo->getClientOriginalName(), 'public');

        // Reset the file input field
        $this->reset('photo');

        // Flash a success message
        session()->flash('success', 'Picture uploaded successfully!!!');

                                         // Clear the cache for uploaded files (if necessary)
        Cache::forget('uploaded_files'); // Ensure the cache is updated with the new file list

        // Optionally, return the file path or perform additional actions
        return $filePath;
    }

    public function loadFiles()
    {
        $directoryPath = public_path('storage');

        // Cache the files list for 60 minutes
        $files = Cache::remember('uploaded_files', 60, function () use ($directoryPath) {
            if (File::exists($directoryPath)) {
                return collect(File::allFiles($directoryPath))
                    ->reject(function ($file) {
                        // Exclude files inside 'pdfs' folder
                        return str_contains($file->getPath(), 'pdfs');
                    })
                    ->sortByDesc(function ($file) {
                        return $file->getMTime();
                    })
                    ->map(function ($file) {
                        return [
                            'name'  => $file->getFilename(),
                            // Maintain relative path if needed
                            'path'  => asset('storage/' . $file->getFilename()),
                            'mtime' => $file->getMTime(),
                        ];
                    })
                    ->toArray();
            }

            return [];
        });

        // Apply search filter
        if ($this->search) {
            $searchTerm = strtolower($this->search);
            $files      = collect($files)->filter(function ($file) use ($searchTerm) {
                return str_contains(strtolower($file['name']), $searchTerm);
            })->values()->toArray();
        }

        // Pagination logic
        $perPage      = 50;
        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $filesForPage = collect($files)
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

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
        $this->reset(['selectedFile', 'search', 'photo']);
    }

    public function saveImage($imageName)
    {
        $shop_image = $imageName;
        $eBay_image = substr($shop_image, 0, -5) . 'e.jpg';

        Item::where('id', $this->itemId)->update([
            'photo'         => $shop_image,
            'pix_path'      => $shop_image,
            'pix_path_eBay' => $eBay_image,
            'is_npr'        => 'N',
        ]);

        session()->flash('success', 'Item photo and pix paths updated successfully!');
    }

    public function render()
    {
        $files = $this->loadFiles();

        return view('livewire.image-selector', compact('files'));
    }
}
