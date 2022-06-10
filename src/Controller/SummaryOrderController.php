<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\Cart;
use Mpdf\HTMLParserMode;
use App\Entity\OrderDetails;
use App\Services\PdfService;
use Mpdf\Http\CurlHttpClient;
use App\Repository\ImageRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderDetailsRepository;
use Sasedev\MpdfBundle\Factory\MpdfFactory;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SummaryOrderController extends AbstractController
{

    
    /**
     * @Route("/facture/{id}", name="summary_order")
     */
    public function index($id,PdfService $pdf,Order $order,OrderDetailsRepository $orderDetailsRepository,OrderRepository $orderRepository): Response
    {
        $html = $this->renderView('summary_order/index.html.twig', [
            'order' => $order,
            'orderDetails'=>$orderDetailsRepository->findByMyOrder($order),
            
        ]);
        $pdf->showPDF($html);
        
        return $this->render('summary_order/index.html.twig', [
            'order' => $order,
            'orderDetails'=>$orderDetailsRepository->findByMyOrder($order)
        ]);
    }
}