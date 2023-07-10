<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function add(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findEventsByUser(Users $user)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.studentClass', 'sc')
            ->leftJoin('sc.students', 's')
            ->leftJoin('e.students', 'u') // Ajout de la jointure avec la relation ManyToMany users
            ->andWhere('t = :user OR s = :user OR u = :user') // Ajout de la condition pour vérifier la relation avec users
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }


    public function findReservedRooms($start): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('r')
            ->from('App\Entity\Room', 'r')
            ->leftJoin('e.room', 'room')
            ->andWhere(':start BETWEEN e.start AND DATE_ADD(e.start, e.duration, \'MINUTE\')')
            ->setParameter('start', new \DateTime($start))
            ->andWhere('room.id = r.id');

        $reservedRooms = $qb->getQuery()->getResult();

        return $reservedRooms;
    }





//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
