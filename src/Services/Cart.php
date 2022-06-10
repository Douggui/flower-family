<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\Length;

Class Cart{

 private $session;

 public function __construct(SessionInterface $session){
     return $this->session=$session;
 }

 /*function return the cart */
 public function getCart(){
     
    return $this->session->get('cart',[]);
    
 }

/*function used to add products to the cart */
 public function add($id,$quantity,$option)
{
    $cart=$this->session->get('cart',[]);
    
    /*if there is not product yet with the defined id($id) we add it with its option and quantity
     else we increse the quantity  */
    if (!empty($cart[$id]) ) {
        if($cart[$id]['specification']==$option){
            $cart[$id]=[
                'quantity'=>$cart[$id]['quantity']+=$quantity,
                'specification'=>$option,
                
            ];
        }else{
            $cart[$id]=[
                'quantity'=> $quantity,
                'specification'=>$option
            ];
        }     
    }else{
        $cart[$id]=[
            'quantity'=> $quantity,
            'specification'=>$option
        ];
    } 
    
    $this->session->set('cart', $cart);
    
}

/*function used to remove cart */
public function removeCart(){
     
   // return $this->session->remove('cart');
   return $this->session->set('cart',[]);


}

/*function used to decrease the product's quantity that have the defined id ($id) */
public function decreaseProduct($id){
  $cart= $this->session->get('cart',[]);

  if($cart[$id]['quantity']>1){
      $cart[$id]['quantity']--;
  }
  else{
      unset($cart[$id]);
  }
    return $this->session->set('cart',$cart);
}

/*function used to increase the product's quantity that have the defined id ($id) */
public function increaseProduct($id){
    $cart=$this->session->get('cart',[]);
    $cart[$id]['quantity']++;
    return $this->session->set('cart',$cart);
}

/*function used to remove the product that have the defined id ($id) */
public function removeProduct($id){
    $cart=$this->session->get('cart',[]);
    unset($cart[$id]);
   return $this->session->set('cart',$cart);
}

}


?>