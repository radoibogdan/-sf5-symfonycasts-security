<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name:'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername() // helper pour précompléter les champs dans login.html.twig si login échoue
        ]);
    }

    #[Route('/logout', name:'app_logout')]
    public function logout()
    {
       throw new \Exception('logout should not be reached');
    }


    /**
     * @Route("/authenticate/2fa/enable", name="app_2fa_authenticate")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function enable2fa(TotpAuthenticatorInterface $totpAuthenticator, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if (!$user->isTotpAuthenticationEnabled()) {
            $user->setTotpSecret($totpAuthenticator->generateSecret());
            $entityManager->flush();
        }
        return $this->render('security/enable2fa.html.twig');
    }

    /**
     * Renvoie uniquement le qrcode(string) qui est utilisé dans sur la route app_2fa_authenticate dans une balise img
     * Todo : IsGranted as ROLE_USER or IS_AUTHENTICATED_FULLY ? Check what's better.
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/authentication/2fa/qr-code", name="app_qr_code")
     */
    public function authenticationQrCode(TotpAuthenticatorInterface $totpAuthenticator)
    {
        $qrCodeContent = $totpAuthenticator->getQRContent($this->getUser());
        $result = Builder::create()
            ->data($qrCodeContent)
            ->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
