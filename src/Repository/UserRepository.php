<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return QueryBuilder
     */
    public function findAllUsersQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'comments')
            ->leftJoin('u.comments', 'comments')
        ;
    }

    /**
     * @return QueryBuilder
     */
    public function findNonDeletedUser(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'comments')
            ->where('u.deleted_at is NULL')
            ->leftJoin('u.comments', 'comments')
        ;
    }

    /**
     * @param User $currentUser
     * @return array|User[]|null
     */
    public function finLatestUser(User $currentUser): ?array
    {
        return $this->createQueryBuilder('u')
            ->where('u.status = 1 and u.id != :id')
            ->setParameter(':id', $currentUser->getId())
            ->orderBy('u.id', 'desc')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }
}
