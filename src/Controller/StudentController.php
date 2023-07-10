<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @Route("/eleves", name="student_home")
     */
    public function index(UsersRepository $usersRepository): Response
    {        
        $user = $this->getUser();

        // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
        $events = $user->getEventsteacher()->toArray();
        $userData = $usersRepository->findByrolesStudent($events);

        return $this->render('student/index.html.twig', [
            'userStudent' => $userData,
        ]);
    }

    /**
     * @Route("/eleves/{id}", name="student_show")
     */
    public function showstudent(UsersRepository $usersRepository, Users $users): Response
    {
        $user = $this->getUser();

        // Récupérer la studentClass de l'utilisateur
        $studentClass = $user->getStudentClass();
        // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
        $events = $user->getEventsteacher()->toArray();
        if ($studentClass) {
            $events = array_merge($events, $studentClass->getEvents()->toArray());
        }
        $userData = $usersRepository->findByrolesStudent($events);


        return $this->render('student/index.html.twig', [
            'userStudent' => $userData,
            'user'=>$users
        ]);
    }
}
