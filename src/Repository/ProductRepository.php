<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

     /**
      * @return Product[] Returns an array of Product objects
      */
    public function findByCategoryId($value): array
    {
        if($value == null || $value == 0){
            return $this->createQueryBuilder('p')
                ->andWhere('NOT p.available = 0')
                ->orderBy('p.id', 'ASC')
                ->getQuery()
                ->getResult()
                ;
        }else{
            return $this->createQueryBuilder('p')
                ->andWhere('p.category = :val')
                ->setParameter('val', $value)
                ->andWhere('NOT p.available = 0')
                ->orderBy('p.id', 'ASC')
                ->getQuery()
                ->getResult()
                ;
        }
    }

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
