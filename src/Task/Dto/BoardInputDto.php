<?php

namespace Tasklist\Task\Dto;

use DateTimeImmutable;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BoardInputDto.*/
class BoardInputDto
{
    /**
     * Tasks.
     *
     * @var TaskInputDto[]
     * @Type("array<Tasklist\Task\Dto\TaskInputDto>")
     *
     * @Assert\Valid
     */
    private $tasks;

    /**
     * BoardInputDto constructor.
     *
     * @param TaskInputDto[] $tasks
     */
    public function __construct(
        array $tasks
    ) {
        $this->tasks = $tasks;
    }

    /**
     * Get tasks.
     *
     * @return TaskInputDto[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
