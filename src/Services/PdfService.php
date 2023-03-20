<?php
namespace App\Services;
use Dompdf\Dompdf;


class PdfService{

    private $dompdf;

    public function __construct()
    {
        $this->dompdf = new Dompdf();
    }

    public function createPDF($htmlTemplate)
    {
        $this->dompdf->loadHtml($htmlTemplate);
        // Render the HTML as PDF
        $this->dompdf->render();
        $this->dompdf->output();
    }
    public function showPDF($htmlTemplate){
        
        $this->dompdf->loadHtml($htmlTemplate);
        $this->dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $this->dompdf->render();
        // Output the generated PDF to Browser
       $this->dompdf->stream('facture.pdf',[
           'Attachement'=>true
       ]);
    }

    
}

?>