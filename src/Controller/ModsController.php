<?php

namespace App\Controller;


use DateTime;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\SubCategory;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Services\Paginator;
use App\Services\FullProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModsController extends AbstractController
{
    /**
     * @Route("/{category}/{subCategory}/{page}", name="products")
     */
    public function products($category,$subCategory ,Paginator $paginator,FullProduct $fullProduct,$page=1,SubCategoryRepository $subCatRepo,ProductRepository $repo , CategoryRepository $categoryRepository): Response
    {
        $category=$categoryRepository->findOneByName($category);
        $subCategory=$subCatRepo->findOneByName($subCategory);
        
        if(!$category || !$subCategory) return $this->redirectToRoute('home');
        /* find the id of the subCategory*/
        $idSubCategory=$subCatRepo->findOneByName($subCategory->getName());
        
        /* find all products relate to the subCategory */
        $data=$repo->findBySubCategory($idSubCategory->getId());
        
        /* paginate the products($data) */ 
        $products=$paginator->pagination($data,$page,28);
        
        return $this->render('mods/index.html.twig', [
            'products'=>$fullProduct->getProductsInformation($products),
            'productPagination'=>$products,
            'category'=>$category,
            'subCategory'=>$subCategory,
        ]);
    }
    /**
     * @Route("/e-cigarette/mods/{page}", name="mods")
     */
    public function index(Paginator $paginator,FullProduct $fullProduct,int $page=1,SubCategoryRepository $subCatRepo,ProductRepository $repo): Response
    {
        /* find the id of the subCategory*/
        $idSubCategory=$subCatRepo->findOneByName('mods');
        
        /* find all products relate to the subCategory */
        $data=$repo->findBySubCategory($idSubCategory->getId());
        
        /* paginate the products($data) */ 
        $products=$paginator->pagination($data,$page,28);
        
        return $this->render('mods/index.html.twig', [
            'products'=>$fullProduct->getProductsInformation($products),
            'productPagination'=>$products
        ]);
    }
    /**
     * @Route("/{category}/{subCategory}/details-produit/{slug}", name="product_show_details")
     */
    public function ProductShow(string $category,string $subCategory,FullProduct $fullProduct,string $slug,ProductRepository $productRepository): Response
    {   
       
        $product=$productRepository->findOneBySlug($slug);
        if(!$product) return $this->redirectToRoute('home');
        return $this->render('ecigarette/product_show.html.twig', [
            /* return all the informations of product found by the param converter to twig */
            
            'productInformation'=>$fullProduct->getFullProductInformation($product),
            'category'=>$category,
            'subCategory'=>$subCategory,
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