<?php

namespace App\Controller;

use App\Entity\Addresse;
use App\Form\AddresseType;
use App\Repository\AddresseRepository;
use App\Services\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddresseController extends AbstractController
{
    /**
     * @Route("/compte/ajouter-une-adresse", name="add_addresse")
     */
    public function index(Cart $cart,Request $request,EntityManagerInterface $manager): Response
    {
        
        $addresse=new Addresse();
        $form=$this->createForm(AddresseType::class,$addresse);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $addresse->setCountry('FR');
            $addresse->setUser($this->getUser());
            $manager->persist($addresse);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre adresse a bien été ajouter'
            );
            if(count($cart->getCart())>0){
                return $this->redirectToRoute('order'); 
            }
            return $this->redirectToRoute('account');

        }
        return $this->render('addresse/index.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="update_addresse")
     */
    public function UpdateAddresse(Addresse $addresse,Request $request,EntityManagerInterface $manager): Response
    {
        if(!$addresse){
            return $this->redirectToRoute('account');
        }
        if($addresse->getUser()!=$this->getUser()){
            return $this->redirectToRoute('account');
        }
        $form=$this->createForm(AddresseType::class,$addresse);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $addresse->setCountry('FR');
            $addresse->setUser($this->getUser());
            $manager->persist($addresse);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre adresse a été mise à jour'
            );
            return  $this->redirectToRoute('account');
        }
        return $this->render('addresse/updateAddresse.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="delete_addresse")
     */
    public function DeleteAddresse(Addresse $addresse,EntityManagerInterface $manager): Response
    {
        if(!$addresse){
            return $this->redirectToRoute('account');
        }
        if($addresse->getUser()!=$this->getUser()){
            return $this->redirectToRoute('account');
        }
        $manager->remove($addresse);
        $manager->flush();
        $this->addFlash(
            'success',
            'votre adresse a bien été supprimer'
        );

        return $this->redirectToRoute('account');
    }
}