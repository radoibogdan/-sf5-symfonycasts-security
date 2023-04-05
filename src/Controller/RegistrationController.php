<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        FormLoginAuthenticator $formLoginAuthenticator,
        VerifyEmailHelperInterface $verifyEmailHelper,
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            # Auto Login de l'user après enregistrement, Renvoie une Redirect Response de l'authentificateur utilisé
            # return $userAuthenticator->authenticateUser(
            #     $user,
            #     $formLoginAuthenticator,
            #     $request
            # );

            # Verify Email
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email', # route name to the verification route
                $user->getId(),               # used to generate token_url
                $user->getEmail(),            # used to generate token_url
                ['id' => $user->getId()]      # used to query for the user in app_verify_email
            );

            // TODO: In a real app, send this as an email
            $this->addFlash('success', 'Confirm your email at '.$signatureComponents->getSignedUrl());

            // Default
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper)
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(), # le lien cliqué
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }

        # A ce point, le lien cliqué dans l'email est validé.
        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Le compte est validé. Vous pouvez vous connecter.');

        # Rediriger ou logger automatiquement le user comme dans la route "register"
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/verify/resend", name="app_verify_resend_email")
     */
    public function resendVerifyEmail(VerifyEmailHelperInterface $verifyEmailHelper)
    {
        # TODO -- faire un formulaire dans le template, quand il est posté envoyer l'email avec le token
        # $form = new Form();
        # if ($form->isSubmitted() && $form->isValid()) {
            # Envoi Email avec token comme dans la partie register
            # Verify Email
            # --- NE FONCTIONNE PAS ---
            # $user = $this->getUser();
            # $signatureComponents = $verifyEmailHelper->generateSignature(
            #     'app_verify_email', # route name to the verification route
            #     $user->getId(),               # used to generate token_url
            #     $user->getEmail(),            # used to generate token_url
            #     ['id' => $user->getId()]      # used to query for the user in app_verify_email
            # );
#
            # // TODO: In a real app, send this as an email
            # $this->addFlash('success', 'Confirm your email at '.$signatureComponents->getSignedUrl());
        # }
        return $this->render('registration/resend_verify_email.html.twig');
    }
}
