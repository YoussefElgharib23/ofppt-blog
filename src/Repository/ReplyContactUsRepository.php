<?php

namespace App\Repository;

use App\Entity\ReplyContactUs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReplyContactUs|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReplyContactUs|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReplyContactUs[]    findAll()
 * @method ReplyContactUs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReplyContactUsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReplyContactUs::class);
    }

    // /**
    //  * @return ReplyContactUs[] Returns an array of ReplyContactUs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReplyContactUs
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
