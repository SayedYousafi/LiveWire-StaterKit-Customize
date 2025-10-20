<?php
namespace App\Livewire;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Unusedpdfs extends Component
{
    public $result = [];
    public $reverse =[];
    //public $unusedpdfs =[];
    public function render()
    {
        //$pdfs = collect(Storage::disk('public')->files('pdfs'))->toArray();
        $pdfs = collect(Storage::disk('public')->files('pdfs'))->filter(function ($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
    });
        //$atts = Attachment::pluck('path')->filter()->unique()->toArray();
        $atts = Attachment::pluck('path')->filter()->unique();
        
        //dd($pdfs, $atts);
        //$this->result = array_diff($pdfs, $atts);
        $this->result = $pdfs->diff($atts)->values();
        $this->reverse = $atts->diff($pdfs)->values();

        //dd($this->result,  $this->reverse);

        return view('livewire.unusedpdfs')->with(
            [
                'unusedpdfs' => $this->result,
            ]);
    }

public function delete($fileName)
    {
        $path = 'pdfs/' . $fileName;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);

            // Dispatch browser event for toast
            $this->dispatch('toast', [
                'message' => "Deleted {$fileName} successfully ✅",
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('toast', [
                'message' => "File {$fileName} not found ❌",
                'type' => 'error'
            ]);
        }
    }
}
