<?php

namespace App\Repository;

use App\Entity\CartPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartPosition[]    findAll()
 * @method CartPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartPosition::class);
    }

    // /**
    //  * @return CartPosition[] Returns an array of CartPosition objects
    //  */
    public function findByUserId($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.User = :val')
            ->setParameter('val', $value)
            ->andWhere('c.cartOrder IS NULL')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?CartPosition
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
