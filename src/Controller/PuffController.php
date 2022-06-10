<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CaracteristicDetailRepository;
use App\Repository\ImageRepository;
use App\Repository\OptionRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Services\FullProduct;
use App\Services\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PuffController extends AbstractController
{
    /**
     * @Route("/e-cigarette/puff/{page}", name="puff")
     */
    public function index(Paginator $paginator,FullProduct $fullProduct,$page=1,SubCategoryRepository $subCatRepo,ProductRepository $repo): Response
    {
         /* find the id of the subCategory*/
        $idSubCategory=$subCatRepo->findOneByName('puffs');

        /* find all products relate to the subCategory */
        $data=$repo->findBySubCategory($idSubCategory->getId());

         /* paginate the products($data) */ 
         $products=$paginator->pagination($data,$page,12);
        
        return $this->render('puff/index.html.twig', [
            'products'=>$fullProduct->getProductsInformation($products),
            'productPagination'=>$products
        ]);
    }
    
}