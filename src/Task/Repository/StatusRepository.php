<?php

namespace Tasklist\Task\Repository;

use Tasklist\Task\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Status|null find($id, $lockMode = null, $lockVersion = null)
 * @method Status|null findOneBy(array $criteria, array $orderBy = null)
 * @method Status[]    findAll()
 * @method Status[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    public function getIndexedById() {
        $indexed = [];
        $statuses = $this->findAll();

        foreach ($statuses as $status) {
            $indexed[$status->getId()] = $status;
        }

        return $indexed;
    }
}
