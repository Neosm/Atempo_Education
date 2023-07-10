<?php

namespace App\Repository;

use App\Entity\Matieres;
use App\Entity\Notes;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notes>
 *
 * @method Notes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notes[]    findAll()
 * @method Notes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notes::class);
    }

    public function add(Notes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Notes[] Returns an array of Notes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notes
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findByMonthAndMatiere(Users $user, Matieres $matiere, string $month)
{
    // Récupérer la première note de l'utilisateur pour la matière donnée
    $firstNote = $this->createQueryBuilder('n')
        ->leftJoin('n.user', 'u')
        ->addSelect('u')
        ->leftJoin('n.matiere', 'm')
        ->addSelect('m')
        ->where('u = :user')
        ->andWhere('m = :matiere')
        ->orderBy('n.date', 'ASC')
        ->setParameter('user', $user)
        ->setParameter('matiere', $matiere)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

    if ($firstNote === null) {
        // Aucune note trouvée, retourner un tableau vide
        return [];
    }

    // Récupérer la date de début des notes
    $startDate = $firstNote->getDate();

    // Récupérer la date de fin du mois donné
    $endDate = new \DateTime($month . '-01');
    $endDate->modify('last day of this month');

    return $this->createQueryBuilder('n')
        ->leftJoin('n.user', 'u')
        ->addSelect('u')
        ->leftJoin('n.matiere', 'm')
        ->addSelect('m')
        ->where('u = :user')
        ->andWhere('m = :matiere')
        ->andWhere('n.date BETWEEN :startDate AND :endDate')
        ->setParameter('user', $user)
        ->setParameter('matiere', $matiere)
        ->setParameter('startDate', $startDate)
        ->setParameter('endDate', $endDate)
        ->getQuery()
        ->getResult();
}





}
