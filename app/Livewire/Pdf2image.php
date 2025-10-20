<?php

namespace App\Livewire;

use Livewire\Component;
use Org_Heigl\Ghostscript\Ghostscript;

class Pdf2image extends Component
{
    public function render()
    {
        $pathToPdf ="C:\\Users\\Administrator.GTECH\\Herd\\newgtech\\storage\\app\\public\\pdfs\\kobash.pdf";
        $pathToImage = "C:\\Users\\Administrator.GTECH\\Herd\\newgtech\\storage\\app\\public\\temp_pdf_images";
        $pdf = new \Spatie\PdfToImage\Pdf($pathToPdf);

        Ghostscript::setGsPath("C:\Program Files\gs\gs10.05.1\bin\gswin64c.exe");
        $pdf->saveImage($pathToImage);
        return view('livewire.pdf2image');
    }
}
