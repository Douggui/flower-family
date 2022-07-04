<?php
namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mail{
     
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer=$mailer;
    }

    public function sendMail($to,$subject,$htmlTemplate,$variables){
        $email = (new TemplatedEmail())
                ->from('narjes@stilvoll.fr')
                ->cc('narjes@gmail.com')
                ->to($to)
                ->subject($subject)
                // path of the Twig template to render
                ->htmlTemplate($htmlTemplate)
                // pass variables (name => value) to the template
                ->context(
                    $variables
                );
            $this->mailer->send($email);
        
    }
}
?>