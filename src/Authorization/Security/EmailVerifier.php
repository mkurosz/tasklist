<?php

namespace Tasklist\Authorization\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Tasklist\User\Repository\UserRepository;

class EmailVerifier
{
    /**
     * Verify email helper interface.
     *
     * @var VerifyEmailHelperInterface
     */
    private $verifyEmailHelper;

    /**
     * Mailer interface.
     *
     * @var MailerInterface
     */
    private $mailer;

    /**
     * Entity manager interface.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * User repository.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * EmailVerifier constructor.
     *
     * @param VerifyEmailHelperInterface $helper
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        VerifyEmailHelperInterface $helper,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $verifyEmailRouteName
     * @param UserInterface $user
     * @param TemplatedEmail $email
     * @throws TransportExceptionInterface
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getEmail(),
            ['id' => base64_encode($user->getEmail())]
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAt'] = $signatureComponents->getExpiresAt();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @param Request $request
     *
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request): void
    {
        $emailEncoded = $request->query->get('id');

        if (empty($emailEncoded)) {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => base64_decode($emailEncoded)]);

        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
