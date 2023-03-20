<?php

namespace App\Controller;

use App\Services\Cart;
use App\Entity\Product;
use App\Repository\ImageRepository;
use App\Repository\StockRepository;
use App\Repository\OptionRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function addProduct(Cart $cart,Request $request,$id,ProductRepository $repo,StockRepository $stockRepo,OptionRepository $optionRepo): Response
    {
        $data=json_decode($request->getContent(),true);
        if($data!=[]){
            $productId=$data['productId'];
            $quantity=$data['finalQuantity'];
            if(isset($data['optionColor'])){
                $color=$data['optionColor']; 
            }else{
                $color='';
            }
            $product=$repo->findOneById($productId);
           // dd($optionRepo->findOneBy(['product'=>$product,'name'=>$color]));
            // $option=$optionRepo->findOneBy(['products'=>$product,'name'=>$color]);
            $option=$optionRepo->getProductOption($product,$color);
            $stock=$stockRepo->findOneBy(['product'=>$product,'productOption'=>$option])->getStock();
            $cartProduct=$cart->getCart();
            
            $cart->add($id,$quantity,$color,$stock);
            
            return new JsonResponse(['message'=>'produit '.$product->getName().' ajouter au panier','status'=>'success'],200);
        }else{
            
            return new JsonResponse(['message'=>'une erreur est survenue veuillez réessayer plus tard','status'=>'danger'],400);
        
        }
        
    }

    
     /**
     * @Route("panier/ajouter/{id}/{option?}", name="increase_product" )
     */
    public function increaseProduct(?string $option,Cart $cart,int $id,Product $product,StockRepository $stockRepo,OptionRepository $optionRepo): Response
    {
       
        $option=$optionRepo->getProductOption($product,$option);
        $stock=$stockRepo->findOneBy(['product'=>$product,'productOption'=>$option])->getStock();
        //dd($stock,$option);
        $quantity=null;
        $cart1=$cart->getCart();
        //dd($cart);
        $i=0;
       while ($i < count($cart1)) {
        $quantity=$cart1[$id]['quantity'];
        $i++;
       }
        //dd($quantity);
        if(($quantity+1) <= $stock){
            $cart->increaseProduct($id);
        }else{
            $this->addFlash(
                'danger',
                'Impossible d\'augmenter la quantité ,stock insuffisant',
            );
            return $this->redirectToRoute('cart');
        }
        //$cart->increaseProduct($id);
        
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