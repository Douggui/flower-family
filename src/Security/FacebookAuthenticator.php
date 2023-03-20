<?php

namespace App\Security;

use App\Entity\Files;
use App\Entity\NewsletterSubscribers;
use App\Entity\User;
use League\OAuth2\Client\Provider\facebookUser;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FacebookAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('facebook');
        $accessToken = $this->fetchAccessToken($client);
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var facebookUser $facebookUser */
                $facebookUser = $client->fetchUserFromToken($accessToken);

                $email = $facebookUser->getEmail();

                // have they logged in with facebook before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['facebookId' => $facebookUser->getId()]);

                //si déjà logué avec autre compte
                $existingUserEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                //User doesnt exist, we create it !
                if (!$existingUser && !$existingUserEmail) {
                    $existingUser = new User();
                    $existingUser->setEmail($email);
                    $existingUser->setFirstname($facebookUser->getFirstName());
                    $existingUser->setLastname($facebookUser->getLastName());
                    $existingUser->setFacebookId($facebookUser->getId());
                    $existingUser->setIsActive(true);
                    $this->entityManager->persist($existingUser);
                    $this->entityManager->flush();

                
                } else if ($existingUserEmail) {//si déjà existant, on update juste son facebookId
                    $existingUserEmail->setfacebookId($facebookUser->getId());
                    $this->entityManager->persist($existingUserEmail);
                    $this->entityManager->flush();
                }
                return $existingUser ?? $existingUserEmail;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        // change "app_dashboard" to some route in your app
        return new RedirectResponse(
            $this->router->generate('home')
        );

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
       
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}