<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Services\FullProduct;
use App\Services\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GreenhouseController extends AbstractController
{
    /**
     * @Route("/cbd/fleurs-greenhouse", name="greenhouse")
     */
    public function index(Paginator $paginator,FullProduct $fullProduct,$page=1,SubCategoryRepository $subCatRepo,ProductRepository $repo): Response
    {
        /* find the id of the subCategory*/
        $idSubCategory=$subCatRepo->findOneByName('fleurs greenhouse');
        
        /* find all products relate to the subCategory */
        $data=$repo->findBySubCategory($idSubCategory->getId());
        
        /* paginate the products($data) */ 
        $products=$paginator->pagination($data,$page,27);
        
        return $this->render('greenhouse/index.html.twig', [
            'products'=>$fullProduct->getProductsInformation($products),
            'productPagination'=>$products
        ]);
    }
}