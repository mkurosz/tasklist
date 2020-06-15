<?php

namespace Tasklist\Task\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Tasklist\Task\Dto\BoardInputDto;
use Tasklist\Task\Dto\TaskInputDto;
use Tasklist\Task\Repository\BoardRepository;
use Tasklist\User\Entity\User;

/**
 * @ORM\Entity(repositoryClass=BoardRepository::class)
 * @ORM\Table(
 *      name="board",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"owner_id", "date"})}
 * )
 * @UniqueEntity(
 *      fields={"owner","date"},
 *      message="Board for given date and owner already exists in database."
 * )
 * @ExclusionPolicy("all")
 */
class Board implements TimestampableInterface
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
     * Board date.
     *
     * @var DateTimeImmutable
     * @Type("DateTimeInterface<'Y-m-d'>")
     *
     * @ORM\Column(type="date_immutable")
     *
     * @Expose
     */
    private $date;

    /**
     * Board owner.
     *
     * @var User
     * @Type("Tasklist\User\Entity\User")
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="boards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * Tasks.
     *
     * @var Task[]
     * @Type("ArrayCollection<Tasklist\Task\Entity\Task>")
     *
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="board", orphanRemoval=true)
     *
     * @Expose
     */
    private $tasks;

    /**
     * Board constructor.
     *
     * @param DateTimeImmutable $date
     * @param User $owner
     */
    public function __construct(
        DateTimeImmutable $date,
        User $owner
    ) {
        $this->date = $date;
        $this->owner = $owner;
        $this->tasks = new ArrayCollection();
        $this->updateTimestamps();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get date.
     *
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * Get owner.
     *
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * Set owner.
     *
     * @param User $owner
     *
     * @return Board
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get tasks array.
     *
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks->toArray();
    }

    /**
     * Filter task by its id from tasks array.
     *
     * @param int $taskId
     *
     * @return Task|null
     */
    public function filterTaskById(int $taskId): ?Task
    {
        $filtered = $this->tasks->filter(
            function(Task $task) use ($taskId) {
                return $task->getId() === $taskId;
            }
        );

        return $filtered->isEmpty() ? null : $filtered->first();
    }

    /**
     * Add task to tasks collection.
     *
     * @param Task $task
     *
     * @return Board
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
        }

        return $this;
    }

    /**
     * Remove task from tasks collection.
     *
     * @param Task $task
     *
     * @return Board
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
        }

        return $this;
    }

    /**
     * Clone entity.
     *
     * @param DateTimeImmutable $date
     *
     * @return Board
    */
    public function cloneEntity(DateTimeImmutable $date): self
    {
        $clone = clone $this;
        $clone->updateTimestamps();
        $clone->date = $date;
        $clone->tasks = new ArrayCollection();

        foreach ($this->tasks as $task) {
            $clone->addTask(
                $task->cloneEntity($clone)
            );
        }

        return $clone;
    }

    /**
     * Update tasks from BoardInputDto.
     *
     * @param BoardInputDto $dto
     * @param Status[] $statuses
     *
     * @return Board
     */
    public function updateFromDto(BoardInputDto $dto, array $statuses): self
    {
        /* @var $toUpdate TaskInputDto[] */
        $toUpdate = $dto->getTasks();

        foreach ($toUpdate as $taskInputDto) {
            $task = $this->filterTaskById($taskInputDto->getId());

            $task->setStatus($statuses[$taskInputDto->getStatus()->getId()]);
            $task->setPosition($taskInputDto->getPosition());
        }

        try {
            $this->setUpdatedAt(new DateTime());
        } catch (Exception $e) {
        }

        return $this;
    }
}
