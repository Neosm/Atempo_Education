<?php

namespace App\Repository;

use App\Entity\ProgrammesLecons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProgrammesLecons|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgrammesLecons|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgrammesLecons[]    findAll()
 * @method ProgrammesLecons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammesLeconsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgrammesLecons::class);
    }

    // /**
    //  * @return ProgrammesLeconsRepository[] Returns an array of ProgrammesLeconsRepository objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProgrammesLeconsRepository
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
