<?php

namespace Tasklist\Task\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Tasklist\Common\Service\AbstractCrudManger;
use Tasklist\Task\Dto\BoardInputDto;
use Tasklist\Task\Dto\TaskInputDto;
use Tasklist\Task\Entity\Board;
use Tasklist\Task\Entity\Status;
use Tasklist\Task\Entity\Task;
use Tasklist\Task\Repository\StatusRepository;

/**
 * Task CRUD manager.
 */
class TaskCrudManger extends AbstractCrudManger
{
    /**
     * Status repository.
     *
     * @var StatusRepository
     */
    private $statusRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param StatusRepository $statusRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        StatusRepository $statusRepository
    ) {
        parent::__construct($entityManager, $logger);

        $this->statusRepository = $statusRepository;
    }

    /**
     * Update board from input.
     *
     * @param TaskInputDto $taskInput
     * @param Board $board
     *
     * @return Task
     */
    public function createFromDto(TaskInputDto $taskInput, Board $board): Task
    {
        $task = Task::createFromDto(
            $taskInput,
            $board,
            $this->statusRepository->getIndexedById()
        );

        $this->persistEntity($task);

        return $task;
    }

    /**
     * Update status.
     *
     * @param Task $task
     * @param Status $status
     *
     * @return Task
     */
    public function updateStatus(Task $task, Status $status): Task
    {
        $this->persistEntity($task->setStatus($status));

        return $task;
    }

    /**
     * Delete task.
     *
     * @param Task $task
     *
     * @return void
     */
    public function delete(Task $task): void
    {
        $this->removeEntity($task);
    }
}
