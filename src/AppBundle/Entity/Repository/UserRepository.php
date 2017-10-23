<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    const LIMIT = 5;

    public function findMostRecentUsers($max = self::LIMIT)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u.id, u.username')
            ->where('u.permissions NOT LIKE :admin')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($max)
            ->setParameter('admin', '%ROLE_ADMIN%')
            ->getQuery()
        ;

        return $q->getArrayResult();
    }
}
