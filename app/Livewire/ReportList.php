<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ReportList extends Component
{
    public $files = [];

    public function download($filePath)
    {

        return response()->download(storage_path("app/{$filePath}"));
    }

    public function render()
    {
        //dd(Storage::files('exports'));

        $this->files = collect(Storage::files('exports'))
            ->map(fn ($file) => [
                'name' => basename($file),
                'path' => $file,
                'date_exported' => date('Y-m-d H:i:s', Storage::lastModified($file)), // Get file modified date
            ])
            ->sortByDesc('date_exported') // Sort by latest date
            ->values()
            ->toArray();
        //dd($this->files);
        return view('livewire.report-list')->with([
            'files' => $this->files,
        ]);
    }
}
