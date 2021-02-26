<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return QueryBuilder
     */
    public function defaultQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'category')
            ->innerJoin('p.category', 'category')
            ->where('p.status = 0 and p.deleted_at is NULL and category.status = 0 and category.deleted_at is NULL');
    }

    /**
     * @return Post[]|null
     */
    public function find15LatestPosts(): ?array
    {
        return $this->defaultQueryBuilder()
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(15)
            ->getResult()
        ;
    }

    public function findInfPost(int $id): array
    {
        return $this->defaultQueryBuilder()
            ->andWhere('p.id < :id')
            ->orderBy('p.id', 'DESC')
            ->setParameter('id', $id)
            ->getQuery()
            ->setMaxResults(15)
            ->getResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findAllPostsForAdmin(): ?array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'category')
            ->where('p.status != 2')
            ->innerJoin('p.category', 'category')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllPostsForSuperAdmin(): ?array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'category')
            ->innerJoin('p.category', 'category')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array|null
     */
    public function findAllPosts(): ?array
    {
        return $this->defaultQueryBuilder()
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countActivePost(): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin('p.category', 'category')
            ->where('p.status = 0 and category.status = 0 and category.deleted_at is NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function finActivePosts()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'likes')
            ->innerJoin('p.likes', 'likes')
            ->where('p.status = 0')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
