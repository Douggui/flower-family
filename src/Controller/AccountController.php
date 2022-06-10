<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Form\UpdatePasswordType;
use App\Repository\AddresseRepository;
use App\Repository\OrderDetailsRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/compte", name="account")
     */
    public function index(AddresseRepository $repo): Response
    {
       $addresses=$repo->findByUser($this->getUser());
       $orders=$this->getUser()->getOrders();
      
       //dd($addresses);
        return $this->render('account/index.html.twig', [
            'user'=>$this->getUser(),
            'addresses'=>$addresses,
            'orders'=>$orders
        ]);
    }
    /**
     * @Route("/compte/modification-mot-de-passe/{id}", name="account_update_password")
     */
    public function UpdatePasswordUser(EntityManagerInterface $manager,Request $request,$id,UserRepository $repo,UserPasswordHasherInterface $passwordHasher  ): Response
    {
       
       $user=$repo->findOneById($id);
       if(!$user){
        return $this->redirectToRoute('home');
       }
       if($user->getId()!=$this->getUser()->getId()){
        return $this->redirectToRoute('home');
       }
       $form=$this->createForm(UpdatePasswordType::class,$user);
       $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid()){
            if(!$passwordHasher->isPasswordValid($user,$user->getOldPassword())){
                $this->addFlash(
                    'danger',
                    'L\'ancien mot de passe est incorrecte'
                );
            }else{
                $user->setPassword($passwordHasher->hashPassword($user,$user->getNewPassword()));
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifier'
                );
            }
            
            
       }
        return $this->render('account/update-password.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/compte/commande/details/{id}", name="account_order_details")
     */
    public function OrderDetails(ProductRepository $productRepo,Order $order,OrderDetailsRepository $repo): Response
    {
       $orderDetails=$repo->findByMyOrder($order);
      //dd($orderDetails);
       //dd($addresses);
        return $this->render('account/order-details.html.twig', [
            'orderDetails'=>$orderDetails
        ]);
    }
}