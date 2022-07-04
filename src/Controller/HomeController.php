<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\OptionRepository;
use App\Repository\ProductRepository;
use App\Services\FullProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repo,ImageRepository $imageRepo): Response
    {
        $bestsellerProduct=$repo->findByIsBest(true);
        $productInformation=[];
        foreach ( $bestsellerProduct as $product) {
            $productInformation[]=[
                'product'=>$product,
                'image'=>$imageRepo->findOneByProduct($product->getId()),
            ];
            
        }
        $isNewProduct=$repo->findByIsNew(true);
        $productIsNew=[];
        foreach ($isNewProduct as $product) {
            $productIsNew[]=[
                'product'=>$product,
                'image'=>$imageRepo->findOneByProduct($product->getId())
            ];
        }
        
        //dd($productIsNew);
        return $this->render('home/index.html.twig', [
            'products'=>$productInformation,
            'newProducts'=>$productIsNew
            
        ]);
    }
}