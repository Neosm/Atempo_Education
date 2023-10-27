<?php

namespace App\Repository;

use App\Entity\Matieres;
use App\Entity\Notes;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
    * return Users['roles'] Returns an array of Users objects
    */

    public function findByrolesStudent($events)
    {
        $userEvents = array_map(function($event) {
            return $event->getId();
        }, $events);

        return $this->createQueryBuilder('s')
            ->leftJoin('s.events', 'e')
            ->andWhere('e.id IN (:val)')
            ->setParameter('val', $userEvents)
            ->andWhere('s.roles LIKE :role')
            ->setParameter('role', '%["ROLE_STUDENT"]%')
            ->getQuery()
            ->getResult();
    }

    public function findByrolesTeacher($events)
    {
        $userEvents = array_map(function($event) {
            return $event->getId();
        }, $events);

        return $this->createQueryBuilder('t')
            ->leftJoin('t.eventsteacher', 'e')
            ->andWhere('e.id IN (:val)')
            ->setParameter('val', $userEvents)
            ->andWhere('t.roles LIKE :role')
            ->setParameter('role', '%["ROLE_TEACHER"]%')
            ->getQuery()
            ->getResult();
    }


    public function findAllAdminByEcole($ecole)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :val')
            ->andWhere('u.ecoles = :ecole')
            ->setParameter('val', '%["ROLE_ADMIN"]%')
            ->setParameter('ecole', $ecole)
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    public function findAllStudentByEcole($ecole)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :val')
            ->andWhere('u.ecoles = :ecole')
            ->setParameter('val', '%["ROLE_STUDENT"]%')
            ->setParameter('ecole', $ecole)
            ->getQuery()
            ->getResult();
    }
    
    public function findAllTeacherByEcole($ecole)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :val')
            ->andWhere('u.ecoles = :ecole')
            ->setParameter('val', '%["ROLE_TEACHER"]%')
            ->setParameter('ecole', $ecole)
            ->getQuery()
            ->getResult();
    }
    
    public function findWithoutUserAndEcole($userId, $ecole)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :val')
            ->andWhere('u.ecoles = :ecole')
            ->setParameter('val', $userId)
            ->setParameter('ecole', $ecole)
            ->getQuery()
            ->getResult();
    }
    

    public function findUserNotes($user)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.notes', 'n')
            ->addSelect('n')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }


    
}
