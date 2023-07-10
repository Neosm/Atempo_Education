<?php

namespace App\Repository;

use App\Entity\Matieres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matieres>
 *
 * @method Matieres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matieres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matieres[]    findAll()
 * @method Matieres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatieresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matieres::class);
    }

    public function add(Matieres $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Matieres $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Matieres[] Returns an array of Matieres objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Matieres
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function calculateMatiereAverage(array $matiereNotes)
    {
        $totalNotes = 0;
        $totalWeightedSum = 0;

        foreach ($matiereNotes as $note) {
            $coefficient = $note->getCoefficient();
            $totalNotes += $coefficient;
            $totalWeightedSum += $note->getNote() * $coefficient;
        }

        if ($totalNotes === 0) {
            return 0;
        }

        return $totalWeightedSum / $totalNotes;
    }

}
