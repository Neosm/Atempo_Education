<?php

namespace App\Repository;

use App\Entity\Programmes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Programmes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programmes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programmes[]    findAll()
 * @method Programmes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Programmes::class);
    }


    public function getAllUserOfProgramme($programme){
        return $this->createQueryBuilder('p')
            ->leftJoin('p.users', 'pu')
            ->select('pu.id')
            ->Where('p.id = :id')
            ->setParameter('id', $programme)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Programmes[] Returns an array of Programmes objects
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
    public function findOneBySomeField($value): ?Programmes
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
