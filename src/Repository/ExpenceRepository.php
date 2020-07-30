<?php

namespace App\Repository;

use App\Entity\Expence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Expence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expence[]    findAll()
 * @method Expence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expence::class);
    }

    // /**
    //  * @return Expence[] Returns an array of Expence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Expence
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
