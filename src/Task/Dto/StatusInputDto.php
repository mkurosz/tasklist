<?php

namespace Tasklist\Task\Dto;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tasklist\Task\Entity\Status;

/**
 * Class StatusInputDto.
 */
class StatusInputDto
{
    /**
     * Id.
     *
     * @var int|null
     * @Type("integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @Assert\Choice(callback={"Tasklist\Task\Entity\Status", "getStatuses"})
     */
    private $id;

    /**
     * Name.
     *
     * @var string|null
     * @Type("string")
     */
    private $name;

    /**
     * StatusInputDto constructor.
     *
     * @param int|null $id
     * @param string|null $name
     */
    public function __construct(
        ?int $id,
        ?string $name
    ) {
        $this->id = $id;
        $this->name = $name;
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
}
