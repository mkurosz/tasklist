<?php

namespace Tasklist\Authorization\Controller;

use Psr\Log\LoggerInterface;
use Tasklist\Authorization\Form\RegistrationFormType;
use Tasklist\Authorization\Security\EmailVerifier;
use Tasklist\Authorization\Security\LoginFormAuthenticator;
use Tasklist\User\Entity\User;
use Tasklist\User\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Tasklist\User\Service\UserCreator;

class RegistrationController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserCreator $userCreator
     *
     * @return Response
     */
    public function register(Request $request, UserCreator $userCreator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCreator->createOnRegistration($form, $user);

            return $this->redirectToRoute('confirm_email');
        }

        return $this->render('Authorization/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function confirmUserEmail(): Response
    {
        return $this->render('Authorization/registration/confirm.html.twig');
    }

    /**
     * @param Request $request
     * @param EmailVerifier $emailVerifier
     *
     * @return Response
     */
    public function verifyUserEmail(Request $request, EmailVerifier $emailVerifier): Response
    {
        try {
            $emailVerifier->handleEmailConfirmation($request);

            $this->addFlash('success', 'Your email address has been verified. Now you can log in to the app!');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        return $this->redirectToRoute('app_login');
    }
}
