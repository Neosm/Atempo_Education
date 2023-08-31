<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatièresClassesController extends AbstractController
{
    #[Route('/mes-matieres', name: 'my_matieres')]
    public function matiere(): Response
    {
        // Récupérer l'utilisateur actuel
        $user = $this->getUser();
        
        // Récupérer la studentClass de l'utilisateur
        $studentClass = $user->getStudentClass();
        
        // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
        $events = $user->getEvents()->toArray();
        if ($studentClass) {
            $events = array_merge($events, $studentClass->getEvents()->toArray());
        }
        
        // Récupérer toutes les matières liées aux événements
        $matieres = [];
        foreach ($events as $event) {
            $matiere = $event->getMatieres();
            if (!in_array($matiere, $matieres)) {
                $matieres[] = $matiere;
            }
        }
        return $this->render('MatièresClasses/matières.html.twig', [
            'matieres' => $matieres,
        ]);
    }

    #[Route('/mes-classes', name: 'my_classes')]
    public function classes(): Response
    {
        $user = $this->getUser();
        $studentClassesByMatiere = [];
    
        // Récupérer les événements où l'utilisateur est enseignant
        $teacherEvents = $user->getEventsteacher()->toArray();
    
        // Organiser les heures passées par classe d'étudiants et matière
        foreach ($teacherEvents as $event) {
            $eventStudentClass = $event->getStudentClass();
            $matiere = $event->getMatieres();
            $duration = $event->getDuration();
    
            if ($eventStudentClass && $matiere) {
                $matiereName = $matiere->getName();
                $studentClassName = $eventStudentClass->getName();
                
                if (!isset($studentClassesByMatiere[$studentClassName])) {
                    $studentClassesByMatiere[$studentClassName] = [];
                }
    
                if (!isset($studentClassesByMatiere[$studentClassName][$matiereName])) {
                    $studentClassesByMatiere[$studentClassName][$matiereName] = [
                        'students' => [],
                        'totalMinutes' => 0,
                        'passedMinutes' => 0,
                    ];
                }
    
                foreach ($eventStudentClass->getStudents() as $student) {
                    $studentName = $student->getName(); // Remplacez par la méthode réelle pour obtenir le nom de l'étudiant
                    if (!in_array($studentName, $studentClassesByMatiere[$studentClassName][$matiereName]['students'])) {
                        $studentClassesByMatiere[$studentClassName][$matiereName]['students'][] = $studentName;
                    }
                }
    
                $studentClassesByMatiere[$studentClassName][$matiereName]['totalMinutes'] += $duration;
    
                $now = new \DateTime();
                if ($event->getEnd() < $now) {
                    $studentClassesByMatiere[$studentClassName][$matiereName]['passedMinutes'] += $duration;
                }
            }
        }
    
        return $this->render('MatièresClasses/classes.html.twig', [
            'studentClassesByMatiere' => $studentClassesByMatiere,
        ]);
    }
    
}
