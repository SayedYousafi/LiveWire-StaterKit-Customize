<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use Illuminate\Support\Collection;

class MissingImages extends Component
{
    public Collection $missingImages;

    public function mount()
    {
        $this->refreshList();
    }

    public function refreshList()
    {
        $allImages = collect(Storage::disk('public')->files());
        $usedImages = Item::pluck('photo')->filter()->unique();

        $this->missingImages = $allImages->filter(function ($imagePath) use ($usedImages) {
            $imageName = basename($imagePath);
            return !$usedImages->contains($imageName);
        })->values();
    }

    public function deleteImage($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        $this->refreshList(); // refresh list after deletion
    }

    public function render()
    {
        return view('livewire.missing-images');
    }
}
