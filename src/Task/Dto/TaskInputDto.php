<?php

namespace Tasklist\Task\Dto;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tasklist\Task\Entity\Status;

/**
 * Class TaskInputDto.
 */
class TaskInputDto
{
    /**
     * Id.
     *
     * @var int|null
     * @Type("integer")
     *
     * @Assert\NotBlank(groups={"UpdateBoard"})
     * @Assert\Type("integer", groups={"UpdateBoard"})
     */
    private $id;

    /**
     * Name.
     *
     * @var string|null
     * @Type("string")
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $name;

    /**
     * Status.
     *
     * @var StatusInputDto|null
     * @Type("Tasklist\Task\Dto\StatusInputDto")
     *
     * @Assert\NotBlank()
     * @Assert\Type("Tasklist\Task\Dto\StatusInputDto")
     * @Assert\Valid
     */
    private $status;

    /**
     * Position.
     *
     * @var int|null
     * @Type("integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $position;

    /**
     * TaskInputDto constructor.
     *
     * @param int|null $id
     * @param string|null $name
     * @param StatusInputDto|null $status
     * @param int|null $position
     */
    public function __construct(
        ?int $id,
        ?string $name,
        ?StatusInputDto $status,
        ?int $position
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->position = $position;
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get status.
     *
     * @return StatusInputDto|null
     */
    public function getStatus(): ?StatusInputDto
    {
        return $this->status;
    }

    /**
     * Get position.
     *
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }
}
