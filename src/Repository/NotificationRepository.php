<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Notification[]|null
     */
    public function findNotSeenNotifications(): ?array
    {
        return $this->createQueryBuilder('n')
            ->where('n.seen', false)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Notification[]|null
     */
    public function findAllNotifications(): ?array
    {
        return $this->createQueryBuilder('n')
            ->select('n', 'posts')
            ->select('n', 'users')
            ->where('n.deleted_at is null and n.seen = 0')
            ->leftJoin('n.post', 'posts')
            ->leftJoin('n.user', 'users')
            ->orderBy('n.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array|Notification[]
     */
    public function findAllNotificationsForAdmin(): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.deleted_at is null')
            ->leftJoin('n.user', 'user')
            ->leftJoin('n.post', 'post')
            ->orderBy('n.id', 'desc')
            ->getQuery()
            ->getResult()
        ;
    }
}