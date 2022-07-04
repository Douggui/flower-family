<?php

namespace App\Controller;

 
use DateTime;
use App\Entity\Order;
use App\Services\Cart;
use App\Services\Mail;
use App\Entity\Product;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Repository\ImageRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\AddresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderDetailsRepository;
use App\Repository\DeliveryMethodRepository;
use App\Services\PdfService;
use Error;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     */
    public function index(AddresseRepository $addresseRepo,DeliveryMethodRepository $deliveryRepo,Request $request,Cart $cart,ProductRepository $repo,ImageRepository $imgRepo): Response
    {
        
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
        foreach ($fullCart as $product) {
          //dd($product);
          $totalTTC+=($product['product']->getPrice()*$product['quantity'])/100;
      
        }
        
        return $this->render('order/index.html.twig', [
           'products'=>$fullCart,
           'deliveryMethod'=>$deliveryMethod,
           'totalTTC'=>$totalTTC,
           'addresses'=>$UserAddresses
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
}