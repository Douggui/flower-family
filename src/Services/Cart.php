<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Length;

Class Cart{

 private $requestStack;

 public function __construct(RequestStack $requestStack){
    $this->requestStack = $requestStack;
 }

 /*function return the cart */
 public function getCart(){
     
    return $this->requestStack->getSession()->get('cart',[]);
    
 }

/*function used to add products to the cart */
 public function add($id,$quantity,$option,$stock)
{

    $cart=$this->getCart();

    /*if there is not product yet with the defined id($id) we add it with its option and quantity
     else we increase the quantity  */
     if (!empty($cart[$id]) ) {
        if(($cart[$id]['quantity']+$quantity)<=$stock){
            $cart[$id]=[
                'quantity'=>$cart[$id]['quantity']+=$quantity,
                'specification'=>$option,
                
            ];
        }else{
            $cart[$id]=[
                'quantity'=>$cart[$id]['quantity']=$stock,
                'specification'=>$option,
                
            ];
            
        }
        
        
    }else{
        $cart[$id]=[
            'quantity'=> $quantity,
            'specification'=>$option
        ];
    } 
    $this->requestStack->getSession()->set('cart', $cart);
    
}

/*function used to remove cart */
public function removeCart(){
     
   return $this->requestStack->getSession()->remove('cart');


}

/*function used to decrease the product's quantity that have the defined id ($id) */
public function decreaseProduct($id){
  $cart= $this->requestStack->getSession()->get('cart',[]);

  if($cart[$id]['quantity']>1){
      $cart[$id]['quantity']--;
  }
  else{
      unset($cart[$id]);
  }
    return $this->requestStack->getSession()->set('cart',$cart);
}

/*function used to increase the product's quantity that have the defined id ($id) */
public function increaseProduct($id){
    $cart=$this->requestStack->getSession()->get('cart',[]);
    $cart[$id]['quantity']++;
    return $this->requestStack->getSession()->set('cart',$cart);
}

/*function used to remove the product that have the defined id ($id) */
public function removeProduct($id){
    $cart=$this->requestStack->getSession()->get('cart',[]);
    unset($cart[$id]);
   return $this->requestStack->getSession()->set('cart',$cart);
}

}


?>