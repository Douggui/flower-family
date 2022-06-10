<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use App\Services\Mail;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/modifier-mdp", name="reset_password")
     */
    public function index(Mail $mail,Request $request,UserRepository $userRepo,EntityManagerInterface $manager): Response
    {
       if($this->getUser()){
           return $this->redirectToRoute('home');
       }
       if($request->get('email')){
           $user=$userRepo->findOneByEmail($request->get('email'));
           if($user){
               $resetPassword=new ResetPassword();
               $token=uniqid();
               $resetPassword->setCeatedAt(new DateTime())
                            ->setToken($token)
                            ->setUser($user);

                $manager->persist($resetPassword);
                $manager->flush();
                $url='http://'.$_SERVER['HTTP_HOST'].'/reinitialisation-mdp/'.$token;
                $to=$user->getEmail();
                $subject='Réinitialisation du mot de passe';
                $htmlTemplate='emails/reset-password.html.twig';
                $variables=[
                    'username' => $user->getFullName(),
                    'url'=>$url
                ];
                $mail->sendMail($to,$subject,$htmlTemplate,$variables);
            $this->addFlash(
                'success',
                'vous allez recevoir un email pour la réinitialisation de mot de passe'
            );
           }else{
               $this->addFlash(
                   'danger',
                   'cet adresse email n\'existe pas'
               );
           }
           
       }
        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
        ]);
    }
    /**
     * @Route("/reinitialisation-mdp/{token}", name="update_password")
     */
    public function UpdatePassword(UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $manager, Request $request, $token,ResetPasswordRepository $repo): Response
    {
        $resetPassword=$repo->findOneByToken($token);
        if(!$resetPassword){
            $this->redirectToRoute('home');
        }
        $now=new DateTime();
        if($now>$resetPassword->getCeatedAt()->modify('+4 hour')){
            $this->addFlash(
                'danger',
                'le lien de réinitialisation a expiré'
            );
        }
        $user=$resetPassword->getUser();
        $form=$this->createForm(ResetPasswordType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordHasher->hashPassword($user,$user->getNewPassword()));
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Nous avons mis à jour votre mot de passe '
            );
            return $this->redirectToRoute('app_login');
        }

        
        return $this->render('reset_password/update.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}