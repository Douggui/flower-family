<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Stock;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Services\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("panier", name="cart")
     */
    public function index(Cart $cart,ProductRepository $repo,ImageRepository $imgRepo): Response
    {
        $cart=$cart->getCart();
        $fullCart=[];
        foreach ($cart as $key=>$value) {
            $fullCart[]=[
                'product'=>$repo->findOneById($key),
                'image'=>$imgRepo->findOneByProduct($repo->findOneById($key)),
                'quantity'=>$value['quantity'],
                'specification'=>$value['specification']
            ];
        }
        
        return $this->render('cart/index.html.twig', [
            'products'=>$fullCart
        ]);
    }
    /**
     * @Route("panier/{id}", name="cart_add_product",methods={"ADD"} )
     */
    public function addProduct(Cart $cart,Request $request,$id): Response
    {
        $data=json_decode($request->getContent(),true);
        if($data!=[]){
            $quantity=$data['finalQuantity'];
            if(isset($data['optionColor'])){
                $color=$data['optionColor']; 
            }else{
                $color='';
            }
            
            $cart->add($id,$quantity,$color);
           
            return new JsonResponse(['message'=>'produit ajouter au panier','status'=>'success'],200);
        }else{
            
            return new JsonResponse(['message'=>'une erreur est survenue veuillez réessayer plus tard','status'=>'danger'],400);
        
        }
        
           
        
    }
     /**
     * @Route("panier/ajouter/{id}", name="increase_product" )
     */
    public function increaseProduct(Cart $cart,$id): Response
    {
        $cart->increaseProduct($id);
        
        return $this->redirectToRoute('cart');
    }
     /**
     * @Route("panier/diminuer/{id}", name="decrease_product" )
     */
    public function decreaseProduct(Cart $cart,$id): Response
    {
        $cart->decreaseProduct($id);
        
        return $this->redirectToRoute('cart');
    }
    
    /**
     * @Route("panier/supprimer/{id}", name="remove_product" )
     */
    public function removeProduct(Cart $cart,$id): Response
    {
        $cart->removeProduct($id);
        
        return $this->redirectToRoute('cart');
    }
   
}