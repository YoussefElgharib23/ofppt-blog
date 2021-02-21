<?php

namespace App\Repository;

use App\Entity\UserLoginLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserLoginLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLoginLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLoginLogs[]    findAll()
 * @method UserLoginLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLoginLogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLoginLogs::class);
    }

    // /**
    //  * @return UserLoginLogs[] Returns an array of UserLoginLogs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserLoginLogs
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
