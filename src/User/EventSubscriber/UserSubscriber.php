<?php

namespace Tasklist\User\EventSubscriber;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Tasklist\Authorization\Security\EmailVerifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tasklist\User\Event\UserCreated;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * Email verifier.
     *
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EmailVerifier $emailVerifier
     * @param LoggerInterface $logger
     */
    public function __construct(
        EmailVerifier $emailVerifier,
        LoggerInterface $logger
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserCreated::NAME => 'onUserCreated',
        ];
    }

    public function onUserCreated(UserCreated $event)
    {
        try {
            $user = $event->getUser();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(
                $e->getMessage(),
                [
                    'message' => $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'previous_trace' => $e->getPrevious() ? $e->getPrevious()->getTraceAsString() : null,
                ]
            );

            throw new RuntimeException(
                'An error occurred while trying to send new account verification email.',
                500
            );
        }
    }
}
