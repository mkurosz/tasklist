<?php

namespace Tasklist\Common\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * Abstract CRUD manager.
 */
abstract class AbstractCrudManger
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Persist entity.
     *
     * @param object $entity The instance to make managed and persistent.
     *
     * @return void
     *
     * @throws RuntimeException Thrown when creation of new entity fails.
     */
    public function persistEntity($entity): void
    {
        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
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
                sprintf(
                    'An error occurred while trying to persist %s.',
                    get_class($entity)
                ),
                500
            );
        }
    }

    /**
     * Remove entity.
     *
     * @param object $entity The instance to remove.
     *
     * @return void
     *
     * @throws RuntimeException Thrown when removal of new entity fails.
     */
    public function removeEntity($entity): void
    {
        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
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
                sprintf(
                    'An error occurred while trying to remove %s.',
                    get_class($entity)
                ),
                500
            );
        }
    }
}
