<?php

namespace Tasklist\User\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Tasklist\User\Entity\User;
use Tasklist\User\Event\UserCreated;
use Throwable;

/**
 * User creator.
 */
class UserCreator
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * User password encoder.
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create user on successful registration.
     *
     * @param FormInterface $form
     * @param User $newUser
     *
     * @return User
     *
     * @throws RuntimeException Thrown when creation of new user fails.
     */
    public function createOnRegistration(FormInterface $form, User $newUser): User
    {
        // encode the plain password
        $newUser->setPassword(
            $this->passwordEncoder->encodePassword(
                $newUser,
                $form->get('plainPassword')->getData()
            )
        );

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($newUser);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(
                new UserCreated($newUser),
                UserCreated::NAME
            );

            $this->entityManager->commit();
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error(
                $e->getMessage(),
                [
                    'message' => $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'previous_trace' => $e->getPrevious() ? $e->getPrevious()->getTraceAsString() : null,
                ]
            );

            throw new RuntimeException(
                'An error occurred while trying to persist new user.',
                500
            );
        }

        return $newUser;
    }
}
