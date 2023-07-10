<?php

namespace App\Controller;

use App\Repository\NotesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatController extends AbstractController
{   
    private function calculateMatiereAverage(array $matiereNotes)
    {
        if (empty($matiereNotes)) {
            return null;
        }

        $totalNotes = 0;
        $totalWeightedSum = 0;

        foreach ($matiereNotes as $note) {
            $coefficient = $note->getCoefficient();
            $totalNotes += $coefficient;
            $totalWeightedSum += $note->getNote() * $coefficient;
        }

        return $totalNotes !== 0 ? $totalWeightedSum / $totalNotes : null;
    }

        
    private function calculateGeneralAverage(array $monthlyAverages)
    {
        if (empty($monthlyAverages)) {
            return null;
        }

        $totalAverage = array_sum($monthlyAverages);
        $count = count($monthlyAverages);

        return $totalAverage / $count;
    }

    #[Route('/resultat', name: 'app_resultat')]
    public function index(NotesRepository $notesRepository): Response
    {
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
        $prof = [];
        foreach ($events as $event) {
            $matiere = $event->getMatieres();
            $prof = $event->getTeacher();
            if (!in_array($matiere, $matieres)) {
                $matieres[] = $matiere;
            }
        }

    // Récupérer toutes les notes de l'utilisateur
    $notes = $notesRepository->findByUser($user);

        // Calculer la moyenne générale de toutes les matières
        $totalMoyenne = 0;
        $nombreMatieres = count($matieres);

        // Tableau des résultats par matière
        $resultatsMatieres = [];

        // Parcourir les matières
        foreach ($matieres as $matiere) {
            // Récupérer toutes les notes associées à la matière pour l'utilisateur donné
            $matiereNotes = $notesRepository->findBy(['user' => $user, 'matiere' => $matiere]);

            // Calculer la moyenne de la matière
            $matiereMoyenne = $this->calculateMatiereAverage($matiereNotes);
            $totalMoyenne += $matiereMoyenne;


            // Enregistrer les résultats par matière dans le tableau
            $resultatsMatieres[] = [
                'matiere' => $matiere,
                'moyenne' => $matiereMoyenne,
                'professeur' => $prof
            ];
        }


        // Calculer la moyenne générale de toutes les matières
        $moyenneGenerale = $totalMoyenne / $nombreMatieres;

        // Rendu de la vue Twig avec les résultats
        return $this->render('resultat/index.html.twig', [
            'resultatsMatieres' => $resultatsMatieres,
            'moyenneGenerale' => $moyenneGenerale
        ]);
    }
}
