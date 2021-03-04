<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]|null
     */
    public function findCategories(): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'posts')
            ->leftJoin('c.posts', 'posts')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getActivePosts()
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'posts')
            ->innerJoin('c.posts', 'posts')
            ->where('posts.status', 0)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCategories()
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'posts')
            ->leftJoin('c.posts', 'posts')
            ->where('posts.deleted_at is not null and posts.status = 0')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findCountActivePosts()
    {
        return $this->createQueryBuilder('c')
            ->select('posts', 'c')
            ->where('posts.status = 0')
            ->having('count(posts.id) > 0')
            ->innerJoin('c.posts', 'posts')
            ->groupBy('posts.id')
            ->getQuery()
            ->getResult()
        ;
    }
}