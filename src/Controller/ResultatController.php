<?php

namespace App\Controller;

use App\Entity\Notes;
use App\Form\NoteType;
use App\Repository\NotesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        // Récupérer toutes les Disciplines liées aux événements
        $matieres = [];
        foreach ($events as $event) {
            $matiere = $event->getMatieres();
            $prof = $event->getTeacher();
            if (!in_array($matiere, $matieres)) {
                $matieres[] = $matiere;
            }
        }

        // Calculer la moyenne générale de toutes les Disciplines
        $totalMoyenne = 0;
        $nombreMatieres = count($matieres);

        // Tableau des résultats par Discipline
        $resultatsMatieres = [];

        // Parcourir les Disciplines
        foreach ($matieres as $matiere) {
            // Récupérer toutes les notes associées à la Discipline pour l'utilisateur donné
            $matiereNotes = $notesRepository->findBy(['user' => $user, 'matiere' => $matiere]);

            // Calculer la moyenne de la Discipline
            $matiereMoyenne = $this->calculateMatiereAverage($matiereNotes);
            $totalMoyenne += $matiereMoyenne;


            // Enregistrer les résultats par Discipline dans le tableau
            $resultatsMatieres[] = [
                'matiere' => $matiere,
                'moyenne' => $matiereMoyenne,
                'professeur' => $prof
            ];
        }

        if ($nombreMatieres > 0 ) {
            // Calculer la moyenne générale de toutes les Disciplines
            $moyenneGenerale = $totalMoyenne / $nombreMatieres;
        } else {
            $moyenneGenerale = -1;
        }


        // Rendu de la vue Twig avec les résultats
        if ($moyenneGenerale >= 0 && $resultatsMatieres) {
            return $this->render('resultat/index.html.twig', [
                'resultatsMatieres' => $resultatsMatieres,
                'moyenneGenerale' => $moyenneGenerale
            ]);
        } else {
            return $this->render('resultat/index.html.twig', [
                'resultatsMatieres' => $resultatsMatieres,
            ]);
        }

    }

    #[Route('/resultat/ajouter', name: 'app_resultat_add')]
    public function add(Request $request): Response
    {
        $note = new Notes();
        $ecole = $this->getUser()->getEcoles();

        $form = $this->createForm(NoteType::class, $note, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setEcoles($ecole);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'La note a été ajoutée avec succès.');
            return $this->redirectToRoute('app_resultat');
        }


        return $this->render('resultat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
