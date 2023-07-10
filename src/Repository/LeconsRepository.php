<?php

namespace App\Repository;

use App\Entity\Lecons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lecons|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lecons|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lecons[]    findAll()
 * @method Lecons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeconsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecons::class);
    }

    // /**
    //  * @return Lecons[] Returns an array of Lecons objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lecons
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
