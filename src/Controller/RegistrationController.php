<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Services\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="registration")
     */
    public function index(Mail $mail ,Request $request,EntityManagerInterface $manager,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user =new User();
        $form=$this->createForm(RegistrationType::class,$user);
        $form->get('agreeTerms')->setData(true);
        $form->handleRequest($request);
        $message='';
        $errors=false;
        if($form->isSubmitted() && $form->isValid()){
            $token=sha1($user->getEmail());
            $url='http://'.$_SERVER['HTTP_HOST'].'/activation-du-compte/'.$user->getEmail().'/'.$token;
            if($form->get('agreeTerms')->getData()==true){
                $user->setPassword($passwordHasher->hashPassword($user,$user->getPassword()));
                $user->setIsActive(0);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'inscription réussie vous allez recevoir un email pour activer votre compte'
                );
                /* send mail of activation to the user*/
                $to=$user->getEmail();
                $subject='Activation du compte';
                $htmlTemplate='emails/account-activation.html.twig';
                $variables=[
                    'username' => $user->getFullName(),
                    'url'=>$url
                ];
                $mail->sendMail($to,$subject,$htmlTemplate,$variables);
            return  $this->redirectToRoute('home');
                 
            }else{
                $errors=true;
                $message='veuillez accepter les termes générale d\'utilisation';
            }
            
            
        }
        return $this->render('registration/index.html.twig', [
            'form'=>$form->createView(),
            'message'=>$message,
            'errors'=>$errors
        ]);
    }

    /**
     * @Route("/activation-du-compte/{email}/{token}", name="activation")
     */
    public function activation(User $user,$token,EntityManagerInterface $manager): Response
    {
        $token_verif=sha1($user->getEmail());
        if($token==$token_verif){
            $user->setIsActive(1);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->render('registration/activation.html.twig', [
           
        ]);
    }
}