<?php

namespace Tasklist\Task\Entity;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JMS\Serializer\Annotation\Type;
use Tasklist\Task\Repository\StatusRepository;

/**
 * @ORM\Entity(repositoryClass=StatusRepository::class)
 */
class Status
{
    /**
     * Status to do.
     *
     * @var int
     */
    public const TODO = 1;

    /**
     * Status in progress.
     *
     * @var int
     */
    public const IN_PROGRESS = 2;

    /**
     * Status done.
     *
     * @var int
     */
    public const DONE = 3;

    /**
     * Id.
     *
     * @var int
     * @Type("integer")
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Name.
     *
     * @var string
     * @Type("string")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Status constructor.
     *
     * @param int $id
     * @param string $name
     */
    public function __construct(
        int $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
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
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get statuses.
     *
     * @return int[]
     */
    public static  function getStatuses(): array
    {
        return [self::TODO, self::IN_PROGRESS, self::DONE];
    }
}
