<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    /**
     * @Route("/professeurs", name="teacher_home")
     */
    public function index(UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        // Récupérer la studentClass de l'utilisateur
        $studentClass = $user->getStudentClass();
        // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
        $events = $user->getEvents()->toArray();
        if ($studentClass) {
            $events = array_merge($events, $studentClass->getEvents()->toArray());
        }
        dump($events);
        $userData = $usersRepository->findByrolesTeacher($events);

        return $this->render('teacher/index.html.twig', [
            'userTeacher' => $userData,
        ]);
    }

    /**
     * @Route("/professeurs/{id}", name="teacher_show")
     */
    public function showteachers(UsersRepository $usersRepository, Users $users): Response
    {
        $user = $this->getUser();

        // Récupérer la studentClass de l'utilisateur
        $studentClass = $user->getStudentClass();
        // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
        $events = $user->getEvents()->toArray();
        if ($studentClass) {
            $events = array_merge($events, $studentClass->getEvents()->toArray());
        }
        $userData = $usersRepository->findByrolesTeacher($events);

        return $this->render('teacher/index.html.twig', [
            'userTeacher' => $userData,
            'user'=>$users
        ]);
    }
}
