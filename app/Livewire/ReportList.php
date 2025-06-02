<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class ReportList extends Component
{
    public $files = [];

    public function mount()
    {
        $this->listFiles();
    }

    public function listFiles()
    {
        $this->files = collect(Storage::files('exports'))
            ->map(fn($file) => [
                'name' => basename($file),
                'path' => $file,
                'date_exported' => date('Y-m-d H:i:s', Storage::lastModified($file)), // Get file modified date
            ])
            ->sortByDesc('date_exported') // Sort by latest date
            ->values()
            ->toArray();
    }

    public function download($filePath)
    {
        
        return response()->download(storage_path("app/{$filePath}"));
    }

    public function render()
    {
        return view('livewire.report-list');
    }
}
