<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Repository\AbsenceRepository;
use App\Repository\ArticlesRepository;
use App\Repository\DelayRepository;
use App\Repository\EventRepository;
use App\Repository\LeconsRepository;
use App\Repository\MatieresRepository;
use App\Repository\NotesRepository;
use App\Repository\ProgrammesRepository;
use App\Repository\RoomRepository;
use App\Repository\StudentClassRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/admin", name="admin_")
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(
        EventRepository $eventRepository, 
        UsersRepository $userRepository, 
        StudentClassRepository $studentClassRepository, 
        RoomRepository $roomRepository,
        NotesRepository $notesRepository,
        MatieresRepository $matieresRepository,
        LeconsRepository $leconsRepository,
        ProgrammesRepository $programmesRepository,
        DelayRepository $delayRepository, 
        AbsenceRepository $absenceRepository,
        ArticlesRepository $articlesRepository): Response
    {
        $ecole = $this->getUser()->getEcoles();

        $students = $userRepository->findAllStudentByEcole($ecole);
        $teachers = $userRepository->findAllTeacherByEcole($ecole);
        $studentClasses = $studentClassRepository->findBy(['ecoles' => $ecole]);
        $rooms = $roomRepository->findBy(['ecoles' => $ecole]);
        $notes = $notesRepository->findBy(['ecoles' => $ecole]);
        $lecons = $leconsRepository->findBy(['ecoles' => $ecole]);
        $programmes = $programmesRepository->findBy(['ecoles' => $ecole]);
        $delays = $delayRepository->findBy(['ecoles' => $ecole]);
        $absences = $absenceRepository->findBy(['ecoles' => $ecole]);
        $latestArticle = $articlesRepository->findBy(['ecoles' => $ecole]);
        $matieres = $matieresRepository->findBy(['ecoles' => $ecole]);
        $events = $eventRepository->findBy(['ecoles' => $ecole]);

        //CALENDAR
        // Récupérer les événements associés à l'utilisateur
        // Convertir les événements en un format compatible avec FullCalendar (par exemple, JSON)
        $formattedEvents = [];
        $objectifs = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'teacher' => $event->getTeacher()->getUserIdentifier(),
                // Ajoutez ici d'autres champs que vous souhaitez afficher dans le calendrier
            ];
            if ($event->getObjectif() !== null ){
                $objectifs[] = [
                    'title' => $event->getTitle(),
                    'start' => $event->getStart()->format('Y-m-d H:i:s'),
                    'objectif' => $event->getObjectif(),
                    'matière' => $event->getMatieres(),
                ];
            }
        }
        // Retourner les événements au format JSON
        $jsonEvents = json_encode($formattedEvents);

        return $this->render('admin/index.html.twig', [
            'dataEvents' => $jsonEvents,
            'students' => $students,
            'teachers' => $teachers,
            'studentClasses' => $studentClasses,
            'rooms' => $rooms,
            'notes' => $notes,
            'matieres' => $matieres,
            'lessons' => $lecons,
            'programs' => $programmes,
            'delays' => $delays,
            'absences' => $absences,
            'latestArticle' => $latestArticle,
        ]);
    }
}
