<?php

namespace Tasklist\User\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tasklist\User\Entity\User;

/**
 * User created event.
 */
class UserCreated extends Event
{
    public const NAME = 'user.created';

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
