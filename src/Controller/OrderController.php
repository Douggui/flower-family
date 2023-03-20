<?php

namespace App\Controller;

 
use DateTime;
use Stripe\Stripe;
use App\Entity\Order;
use App\Services\Cart;
use App\Services\Mail;
use App\Form\OrderType;
use App\Form\AddresseType;
use App\Entity\OrderDetails;
use Stripe\Checkout\Session;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use App\Repository\AddresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DeliveryMethodRepository;
use App\Repository\OptionRepository;
use App\Repository\StockRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     * 
     */
    public function index(AddresseRepository $addresseRepo,DeliveryMethodRepository $deliveryRepo,Request $request,Cart $cart,ProductRepository $repo,ImageRepository $imgRepo,EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $cart=$cart->getCart();
        if(count($cart)===0){
          return $this->redirectToRoute('home');
        }
        $deliveryMethod=$deliveryRepo->findAll();
        
        $UserAddresses=$addresseRepo->findByUser($this->getUser());
        if(count( $UserAddresses)===0){
          $this->addFlash(
            'warning',
            'veuillez renseigner une adresse pour poursuivre votre achat'
          );
          return  $this->redirectToRoute('add_addresse');
        }
        //dd($UserAddresses);
        $fullCart=[];
        foreach ($cart as $key=>$value) {
            $fullCart[]=[
                'product'=>$repo->findOneById($key),
                'image'=>$imgRepo->findOneByProduct($repo->findOneById($key)),
                'quantity'=>$value['quantity'],
                'specification'=>$value['specification']
            ];
           //dd($fullCart);
        }
        $totalTTC=0;
        $isCartValid=true;
        foreach ($fullCart as $product) {
          //dd($product);
          // dd(!$product['product']->getTheStock()>0);
          $totalTTC+=($product['product']->getPrice()*$product['quantity'])/100;
          if(!$product['product']->getTheStock()>0) $isCartValid = false;
        }
        // dd($isCartValid);
        $form=$this->createForm(OrderType::class,null,['user'=>$this->getUser()]);
        $form->handleRequest($request);
        $postal='';
        $deliveryAddress='';
        if($form->isSubmitted() && $form->isValid()){
          //dd($totalTTC);
          //dd($form['Addresse']->getData());
          
          if( $form['Addresse']->getData() != null){
            
            $postal=$form['Addresse']->getData()->getPostal();
            $deliveryAddress=$form['Addresse']->getData()->getAddresse().' '.$form['Addresse']->getData()->getPostal().' '.$form['Addresse']->getData()->getCity();
            
          }
          $deliveryMethod=$form['DeliveryMethod']->getData()->getName();
          if($totalTTC<50 && $deliveryMethod !='click et collecte' || ($totalTTC>50 && $postal!=null && $postal != '06370')){
            $this->addFlash(
              'danger',
              'votre commande n\'est pas éligible à la livraison à domicile'
            );
           return  $this->redirectToRoute('order');
          }
        if($isCartValid){

          $date=new DateTime();
          $order=new Order();
          $order->setUser($this->getUser())
                ->setCreatedAt($date)
                ->setDeliveryMethod($deliveryMethod)
                ->setReference(uniqid().'-'.$date->format('dmY'))
                ->setIsDelivered(0)
                ->setTotal($totalTTC*100)
                ->setStatus(Order::ISPENDING)
                ;
                if($deliveryMethod === 'livraison à domicile'){
                  $order->setAddresse($deliveryAddress);
                }else{
                  $order->setAddresse('');
                }
          
          $products_for_stripe=[];
          foreach ($fullCart as $product) {
            $orderDetails=new OrderDetails();
            $orderDetails->setMyOrder($order)
                        ->setProduct($product['product'])
                        ->setQuantity($product['quantity'])
                        ->setSpecification($product['specification'])
                        ->setPrice($product['product']->getPrice())
                        ->setTotal(($product['product']->getPrice()*$product['quantity']))
                        ;
            $manager->persist($orderDetails);

            $products_for_stripe[] = [
              'price_data' => [
                  'currency' => 'eur',
                  'product_data' => [
                      'name' => $product['product']->getName(),
                      //'specification'=>$product['specification'],

                  ],
                  'unit_amount' => $product['product']->getPrice()

              ],
              'quantity' => $product['quantity'],

            ];

          }
          //dd($products_for_stripe);
          \Stripe\Stripe::setApiKey($this->getParameter('stripe_private_key'));

          $DOMAIN = 'http://localhost:8000';
        //  dd($DOMAIN.'/commande/success/{CHECKOUT_SESSION_ID}');
          $checkout_session = \Stripe\Checkout\Session::create([
            
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => $products_for_stripe,
            'mode' => 'payment',
            'success_url' =>  $DOMAIN.'/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $DOMAIN.'/erreur/{CHECKOUT_SESSION_ID}',
          ]);
        
          $stripeUrl= $checkout_session->url;
          $order->setStripeUrl($stripeUrl);
            $order->setCheckoutSessionId($checkout_session->id);
          
            $manager->persist($order);
            $manager->flush();
            
              return $this->render('order/recap.html.twig', [
                  'stripeUrl' =>$stripeUrl,
                  'products' => $fullCart,
                  'total'=>$totalTTC,
                  'order' => $order
              ]);
          
          }else{
            $this->addFlash(
              'danger',
              'Veuillez mettre à jour votre panier avant de pouvoir continuer'
            );
            return $this->redirectToRoute('cart');
          }

        }
        return $this->render('order/index.html.twig', [
           'products'=>$fullCart,
           'deliveryMethod'=>$deliveryMethod,
           'totalTTC'=>$totalTTC,
           'addresses'=>$UserAddresses,
           'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/success/{CheckoutSessionId}", name="order_success")
     */
    public function success(ProductRepository $productRepo,OptionRepository $optionRepo,StockRepository $stockRepo,Mail $mail  ,Order $order, Request $request, ProductRepository $repo, Cart $cart, EntityManagerInterface $manager, $CheckoutSessionId): Response
    { 

        //on récupére la session pour vérifier le paiment 
        Stripe::setApiKey($this->getParameter('stripe_private_key'));

        $session = Session::retrieve($CheckoutSessionId);
      
        if ($session->payment_status !== "paid") return $this->redirectToRoute('order_cancel', ['checkoutSessionId' => $CheckoutSessionId]);
        //dump($session->payment_status);
        //si il y a de commande ou la commande n'est pas de l'utilisateur en cours on redirige vers la page d'accueil 
        if (!$order || $order->getUser() != $this->getUser()) return $this->redirectToRoute('home');
        //on vide le panier

        //on met le statut de paiment à 1 dans la bdd 
        $order->setIsPaid(1);
        $order->setStatus(Order::PAID);
        $manager->persist($order);
        //on enregistre la bdd
        
        $url='http://'.$_SERVER['HTTP_HOST'].'/compte';
        /** @var User  */
        $to=$this->getUser()->getEmail();
        $subject='Confirmation de commande';
        $htmlTemplate='emails/order-confirmation.html.twig';
        $variables=[
          'username' => $this->getUser()->getFullName(),
          'url'=>$url
        ];
          
        try {
            $mail->sendMail($to,$subject,$htmlTemplate,$variables); 
        } catch (TransportExceptionInterface $e) {
          $mail->sendMail($to,$subject,$htmlTemplate,$variables); 
        }
        $cart1 = $cart->getCart();
       
        foreach ($cart1 as $key=>$value) {
          $product = $productRepo->findOneBy(['id'=>$key]);
          $option=$optionRepo->findOneBy(['product'=>$product,'name'=>$value['specification']]);
          $stock=$stockRepo->findOneBy(['product'=>$product,'productOption'=>$option]);
          // dd($product->getOptions(),$option,$stock);
          $stock ->setStock($stock->getStock() - $value['quantity']);
          $manager->persist($stock);
          
        }
        $manager->flush();
        $cart->removeCart();
        
        return $this->render('order/success.html.twig', [
            'order' => $order,
            //'productsCart' => $productsCart
        ]);

       
    }
    /**
     * @Route("/erreur/{CheckoutSessionId}", name="order_cancel")
     */
    public function error(Order $order, $CheckoutSessionId,EntityManagerInterface $manager): Response
    {
      
        if (!$order || $order->getUser() != $this->getUser()) return $this->redirectToRoute('home');
        Stripe::setApiKey($this->getParameter('stripe_private_key'));
        $order->setStatus(Order::CANCELED);
        $manager->flush();
        $session = Session::retrieve($CheckoutSessionId);
        return $this->render('order/cancel.html.twig', [
            'order' => $order
        ]);
    }

     /**
     * @Route("/commande/payment/{id}", name="order_achieve_payment")
     */
    public function achievePayment(Order $order,AddresseRepository $addresseRepo,DeliveryMethodRepository $deliveryRepo,Request $request,Cart $cart,ProductRepository $repo,ImageRepository $imgRepo,EntityManagerInterface $manager): Response
    {
      
      $checkoutSession=$order->getCheckoutSessionId();
      $url='https://checkout.stripe.com/c/pay/'.$checkoutSession;
      //header('Location: '.$url);
      //dd($checkoutSession);
      return $this->redirectToRoute($url);
      return $this->render('order/achievePayment.html.twig',[
        'url'=>$url,
      ]);
 }

    
    /**
     * @Route("/commande/info", name="order_data",methods={"POST"})
     */
    public function Order(Mail $mail,ProductRepository $repo,Request $request,Cart $cart1,EntityManagerInterface $manager): Response
    {
      $chosenAddresse='';
      $chosenDeliveryMethod='';
      $cart=$cart1->getCart();
      $fullCart=[];
        foreach ($cart as $key=>$value) {
            $fullCart[]=[
                'product'=>$repo->findOneById($key),
                'quantity'=>$value['quantity'],
                'specification'=>$value['specification']
            ];
        }
      $totalTTC=0;
        foreach ($fullCart as $product) {
          $totalTTC+=($product['product']->getPrice()*$product['quantity']);
        }
      $data=json_decode($request->getContent(),true);
        $chosenAddresse=$data['fullAddresse'];
        $chosenDeliveryMethod=$data['deliveryMethod'];
        $order=new Order();
        $date=new DateTime();
        $order->setAddresse($chosenAddresse)
              ->setCreatedAt($date)
              ->setDeliveryMethod($chosenDeliveryMethod)
              ->setIsDelivered(0)
              ->setReference(uniqid())
              ->setTotal($totalTTC)
              ->setUser($this->getUser());
              
              $manager->persist($order);
        
        foreach ($fullCart as $product) {
          $orderDetails=new OrderDetails();
          $orderDetails->setMyOrder($order)
                       ->setProduct($product['product'])
                       ->setQuantity($product['quantity'])
                       ->setSpecification($product['specification'])
                       ->setPrice($product['product']->getPrice())
                       ->setTotal(($product['product']->getPrice()*$product['quantity']));
          $manager->persist($orderDetails);
          
        $manager->flush();
        $cart1->removeCart();
        $url='http://'.$_SERVER['HTTP_HOST'].'/compte';
        $to=$this->getUser()->getEmail();
        $subject='Confirmation de commande';
        $htmlTemplate='emails/order-confirmation.html.twig';
        $variables=[
          'username' => $this->getUser()->getFullName(),
          'url'=>$url
        ];
          
        try {
            $mail->sendMail($to,$subject,$htmlTemplate,$variables); 
        } catch (TransportExceptionInterface $e) {
          $mail->sendMail($to,$subject,$htmlTemplate,$variables); 
        }
        }
        return new JsonResponse(['message'=>'Nous avons bien réçu votre commande ','status'=>'success'],200);
      
    
  
  
    }
    /**
     * @Route("/commande-paiment/info", name="order_payment")
     */
    public function OrderPayNow(Mail $mail,ProductRepository $repo,Request $request,Cart $cart,EntityManagerInterface $manager): Response
    {
      $this->denyAccessUnlessGranted('ROLE_USER');
        \Stripe\Stripe::setApiKey($this->getParameter('stripe_private_key'));

        header('Content-Type: application/json');

        $DOMAIN = $_SERVER['HTTP_HOST'];
        //dd($DOMAIN);
        foreach ($cart as $key=>$value) {
         dd($key,$value);
         $checkout_session = \Stripe\Checkout\Session::create([
          'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'product'=>$repo->findOneById($key)->getName(),
            'price' => $repo->findOneById($key)->getPrice(),
            'quantity' => $value['quantity'],
          ]],
          'mode' => 'payment',
          'success_url' => $DOMAIN . '/success.html',
          'cancel_url' => $DOMAIN . '/cancel.html',
        ]);
        }
        

        //header("HTTP/1.1 303 See Other");
        //header("Location: " . $checkout_session->url);
        return $this->renderView('payment/index.html.twig');  
    }
}