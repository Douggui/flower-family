<?php

namespace App\Controller;

use App\Entity\CaracteristicDetail;
use App\Entity\Comment;
use App\Entity\Option;
use App\Entity\Product;
use App\Form\CommentType;
use App\Repository\CaracteristicDetailRepository;
use App\Repository\CaracteristicRepository;
use App\Repository\ImageRepository;
use App\Repository\OptionRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Services\FullCart;
use App\Services\FullProduct;
use App\Services\Paginator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ModsController extends AbstractController
{
    /**
     * @Route("/e-cigarette/mods/{page}", name="mods")
     */
    public function index(Paginator $paginator,FullProduct $fullProduct,$page=1,SubCategoryRepository $subCatRepo,ProductRepository $repo): Response
    {
        /* find the id of the subCategory*/
        $idSubCategory=$subCatRepo->findOneByName('mods');
        
        /* find all products relate to the subCategory */
        $data=$repo->findBySubCategory($idSubCategory->getId());
        
        /* paginate the products($data) */ 
        $products=$paginator->pagination($data,$page,27);
        
        return $this->render('mods/index.html.twig', [
            'products'=>$fullProduct->getProductsInformation($products),
            'productPagination'=>$products
        ]);
    }
    /**
     * @Route("/ecigarette/produit-details/{slug}", name="ecigarette_show_details")
     */
    public function ProductShow(FullProduct $fullProduct,Product $product): Response
    {   
        return $this->render('ecigarette/product_show.html.twig', [
            /* return all the informations of product found by the param converter to twig*/
            
            'productInformation'=>$fullProduct->getFullProductInformation($product)
        ]);
    }
    /**
     * @Route("/compte/mes-commandes/{slug}/comment", name="comment_product")
     */
    public function comment(Product $product,Request $request,EntityManagerInterface $manager): Response
    {   
        $comment=new Comment();
        $form=$this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new DateTime())
                    ->setProduct($product)
                    ->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                'Le commentaire pour le produit '.$product->getName().' a bien été enregistrée.'
            );
            return $this->redirectToRoute('account');
        }
        
        return $this->render('ecigarette/comment.html.twig', [
          'product'=>$product,
          'form'=>$form->createView()
        ]);
    }


    
}