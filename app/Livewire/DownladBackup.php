<?php

namespace App\Livewire;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DownladBackup extends Component
{
    public function render()
    {
        $directory = 'mySqlBackup';
        $files = collect(Storage::files($directory));
    
        $sortedFiles = $files->sortByDesc(function ($file) {
            return Storage::lastModified($file);
        });
    //dd($sortedFiles);
        return view('livewire.downlad-backup', [
            'files' => $sortedFiles
        ]);
    }
    

    public function download($filePath)
    {
        //dd($filePath);
        return response()->download(storage_path("app/{$filePath}"));
    }
}
