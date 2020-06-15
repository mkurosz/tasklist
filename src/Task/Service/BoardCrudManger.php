<?php

namespace Tasklist\Task\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Tasklist\Common\Service\AbstractCrudManger;
use Tasklist\Task\Dto\BoardInputDto;
use Tasklist\Task\Entity\Board;
use Tasklist\Task\Repository\StatusRepository;

/**
 * Board CRUD manager.
 */
class BoardCrudManger extends AbstractCrudManger
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
     * Create board.
     *
     * @param Board $newBoard
     *
     * @return Board
     */
    public function create(Board $newBoard): Board
    {
        $this->persistEntity($newBoard);

        return $newBoard;
    }

    /**
     * Update board from input.
     *
     * @param Board $board
     * @param BoardInputDto $boardInput
     *
     * @return Board
     */
    public function updateFromDto(Board $board, BoardInputDto $boardInput): Board
    {
        $this->persistEntity(
            $board->updateFromDto(
                $boardInput,
                $this->statusRepository->getIndexedById()
            )
        );

        return $board;
    }
}
