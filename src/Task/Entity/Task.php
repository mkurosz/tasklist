<?php

namespace Tasklist\Task\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Tasklist\Task\Dto\TaskInputDto;
use Tasklist\Task\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 *
 * @ExclusionPolicy("all")
 */
class Task implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * Id.
     *
     * @var int
     * @Type("integer")
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Expose
     */
    private $id;

    /**
     * Name.
     *
     * @var string
     * @Type("string")
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Expose
     */
    private $name;

    /**
     * Board.
     *
     * @var Board
     * @Type("Tasklist\Task\Entity\Board")
     *
     * @ORM\ManyToOne(targetEntity=Board::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $board;

    /**
     * Status.
     *
     * @var Status
     * @Type("Tasklist\Task\Entity\Status")
     *
     * @ORM\ManyToOne(targetEntity=Status::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Expose
     */
    private $status;

    /**
     * Position.
     *
     * @var int
     * @Type("integer")
     *
     * @ORM\Column(type="integer")
     *
     * @Expose
     */
    private $position;

    /**
     * Task constructor.
     *
     * @param string $name
     * @param Board $board
     * @param Status $status
     * @param int $position
     */
    public function __construct(
        string $name,
        Board $board,
        Status $status,
        int $position
    ) {
        $this->name = $name;
        $this->board = $board;
        $this->status = $status;
        $this->position = $position;
        $this->updateTimestamps();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get board.
     *
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * Get status.
     *
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * Set status.
     *
     * @param Status $status
     *
     * @return Task
     */
    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return Task
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Clone entity.
     *
     * @param Board $board
     *
     * @return Task
     */
    public function cloneEntity(Board $board): self
    {
        $clone = clone $this;
        $clone->updateTimestamps();
        $clone->board = $board;

        return $clone;
    }

    /**
     * Create Task from TaskInputDto
     *
     * @param TaskInputDto $taskInputDto
     * @param Board $board
     * @param Status[] $statuses
     *
     * @return Task
     */
    public static function createFromDto(
        TaskInputDto $taskInputDto,
        Board $board,
        array $statuses
    ): self {
        return new self(
            $taskInputDto->getName(),
            $board,
            $statuses[$taskInputDto->getStatus()->getId()],
            $taskInputDto->getPosition()
        );
    }
}
