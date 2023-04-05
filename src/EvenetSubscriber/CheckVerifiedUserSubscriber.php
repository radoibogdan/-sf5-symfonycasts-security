<?php

namespace App\EvenetSubscriber;

use App\Entity\User;
use App\Exception\AccountNotVerifiedAuthenticationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        # On écoute l'event CheckPassportEvent qui est déclenché après la méthode authenticate() des Authenticator
        # Joue la méthode qu'on a créée onCheckPassport. -10 c'est la priorité = à la fin des autres evenements
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginFailureEvent::class => 'onLoginFailure'
        ];
    }

    /**
     * Déclenché quand l'event CheckPassportEvent est dispatché
     */
    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();
        if(!$passport instanceof UserPassportInterface) {
            throw new \Exception('Unexpected passport type');
        }

        $user = $passport->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Unexpected user type');
        }

        # Si le compte n'est pas vérifié => on renvoie vers la méthode onLoginFailure grâce à l'event LoginFailureEvent
        if (!$user->getIsVerified()) { // compte pas vérifié
            # Sans redirection
            # throw new CustomUserMessageAuthenticationException('Validez votre compte avant de vous connecter.');

            # Avec redirection
            throw new AccountNotVerifiedAuthenticationException();
        }
    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        if (!$event->getException() instanceof AccountNotVerifiedAuthenticationException){
            # Ne change rien au comportement habituel
            return;
        }

//        dd($event);
        # Overwrite normal failure behaviour
        $response = New RedirectResponse($this->router->generate('app_verify_resend_email'));
        $event->setResponse($response);
    }

}