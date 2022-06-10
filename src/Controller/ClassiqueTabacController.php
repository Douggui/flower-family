<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Services\FullProduct;
use App\Services\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassiqueTabacController extends AbstractController
{
    /**
     * @Route("/e-liquide/classique/tabac", name="classique_tabac")
     */
    public function index(Paginator $paginator,FullProduct $fullProduct,$page=1,SubCategoryRepository $subCatRepo,ProductRepository $repo): Response
    {

        /* find the id of the subCategory*/
        $idSubCategory=$subCatRepo->findOneByName('tabac classique');
        
        /* find all products relate to the subCategory */
        $data=$repo->findBySubCategory($idSubCategory->getId());
        
       /* paginate the products($data) */ 
       $products=$paginator->pagination($data,$page,27);

        return $this->render('classique_tabac/index.html.twig', [
            'products'=>$fullProduct->getProductsInformation($products),
            'productPagination'=>$products
        ]);
    }
    /**
     * @Route("/e-liquide/produit-details/{slug}", name="eliquide_show_details")
     */
    public function ProductShow(FullProduct $fullProduct,Product $product): Response
    {   
        return $this->render('eliquide/product_show.html.twig', [
            /* return all the informations of product found by the param converter to twig*/
            'productInformation'=>$fullProduct->getFullProductInformation($product)
        ]);
    }
}