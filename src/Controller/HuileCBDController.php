<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Services\FullProduct;
use App\Services\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HuileCBDController extends AbstractController
{
    /**
     * @Route("/cbd/huile-cbd/{page}", name="huile_cbd")
     */
    public function index(Paginator $paginator,FullProduct $fullProduct,$page=1,SubCategoryRepository $subCatRepo,ProductRepository $repo): Response
    {
        /* find the id of the subCategory*/
        $idSubCategory=$subCatRepo->findOneByName('huiles');
        
        /* find all products relate to the subCategory */
        $data=$repo->findBySubCategory($idSubCategory->getId());
        
        //* paginate the products($data) */ 
        $products=$paginator->pagination($data,$page,27);

        
        return $this->render('huile_cbd/index.html.twig', [
            'products'=>$fullProduct->getProductsInformation($products),
            'productPagination'=>$products
        ]);
    }
}