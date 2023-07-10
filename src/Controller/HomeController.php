<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Matieres;
use App\Entity\Users;
use App\Repository\ArticlesRepository;
use App\Repository\EventRepository;
use App\Repository\LeconsRepository;
use App\Repository\NotesRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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


    #[Route("/", name: "home")]
    public function index(UsersRepository $usersRepository, NotesRepository $notesRepository, ArticlesRepository $artsRepo): Response
    {
        $articles = $artsRepo->findBy(['active' =>true], ['id'=>'DESC']);

        // Récupérer l'utilisateur connecté (vous pouvez adapter cela selon votre logique d'authentification)
        $user = $this->getUser();

        //CALENDAR
        // Récupérer les événements associés à l'utilisateur
        $events = $this->getDoctrine()->getRepository(Event::class)->findEventsByUser($user);
        // Convertir les événements en un format compatible avec FullCalendar (par exemple, JSON)
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'room' => $event->getRoom()->getName(),
                'teacher' => $event->getTeacher()->getUsername(),
                // Ajoutez ici d'autres champs que vous souhaitez afficher dans le calendrier
            ];
        }
        // Retourner les événements au format JSON
        $jsonEvents = json_encode($formattedEvents);

        //ELEVES / TEACHER LIST
        if ($user->getRoles() == ["ROLE_STUDENT"]){
            // Récupérer la studentClass de l'utilisateur
            $studentClass = $user->getStudentClass();
            // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
            $events = $user->getEvents()->toArray();
            if ($studentClass) {
                $events = array_merge($events, $studentClass->getEvents()->toArray());
            }
            $userList = $usersRepository->findByrolesTeacher($events);
        } elseif ($user->getRoles() == ["ROLE_TEACHER"]){
            // Récupérer la studentClass de l'utilisateur
            $studentClass = $user->getStudentClass();
            // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
            $events = $user->getEventsteacher()->toArray();
            if ($studentClass) {
                $events = array_merge($events, $studentClass->getEvents()->toArray());
            }
            $userList = $usersRepository->findByrolesStudent($events);
        }

        //ASSIDUITES
        $absences = $user->getAbsences();
        $delays = $user->getDelays();

        //MATIERE RECAP
        
            // Récupérer toutes les matières liées aux événements
            $matieres = [];
            foreach ($events as $event) {
                $matiere = $event->getMatieres();
                if (!in_array($matiere, $matieres)) {
                    $matieres[] = $matiere;
                }
            }

        // Récupérer toutes les notes de l'utilisateur
        $notes = $notesRepository->findByUser($user);

        // Extraire les mois à partir des dates des notes
        $months = [];
        foreach ($notes as $note) {
            $noteMonth = $note->getDate()->format('Y-m');
            if (!in_array($noteMonth, $months)) {
                $months[] = $noteMonth;
            }
        }

        // Initialiser les variables
        $generalMonthlyAverages = [];

        // Parcourir les mois
        foreach ($months as $month) {
            // Initialiser les variables pour le mois
            $monthlyAverages = [];

            // Parcourir les matières
            foreach ($matieres as $matiere) {
                $matiereId = $matiere->getId();

                // Récupérer toutes les notes associées à la matière pour l'utilisateur donné et le mois donné
                $matiereNotes = $notesRepository->findByMonthAndMatiere($user, $matiere, $month);

                // Calculer la moyenne de la matière pour le mois donné
                $matiereMonthlyAverage = $this->calculateMatiereAverage($matiereNotes);
                if ($matiereMonthlyAverage !== null) {
                    $monthlyAverages[] = $matiereMonthlyAverage;
                }
            }

            // Calculer la moyenne générale pour le mois donné
            $generalMonthlyAverage = $this->calculateGeneralAverage($monthlyAverages);
            $generalMonthlyAverages[$month] = $generalMonthlyAverage;
        }

        // Convertir les données en JSON
        $months = array_reverse(array_keys($generalMonthlyAverages));
        $averageValues = array_reverse(array_values($generalMonthlyAverages));
        $months = json_encode($months);
        $averageValues = json_encode($averageValues);

        return $this->render('home/index.html.twig', [
            'userStudent' => $userList,
            'userTeacher' => $userList,
            'absences' => $absences,
            'delays' => $delays,
            'dataEvents' => $jsonEvents,
            'months' => $months,
            'averageValues' => $averageValues,
            'matieres' => $matieres,
            'articles' => $articles,
        ]);

    }

    #[Route('/mentions-legales', name: 'mentions')]
    public function mentions()
    {
        return $this->render('home/mentions.html.twig');
    }

    #[Route('/assiduite', name: 'user_assiduity')]
    public function userAssiduity(): Response
    {
        $user = $this->getUser();

        $absences = $user->getAbsences();
        $delays = $user->getDelays();

        return $this->render('home/assiduite.html.twig', [
            'absences' => $absences,
            'delays' => $delays,
        ]);
    }

    #[Route('/historique/heure/endpoint', name: 'historique_heures_endpoint')]
    public function historiqueHeuresEndpoint(Request $request)
    {
        // Récupérer l'ID de la matière depuis la requête
        $matiereId = $request->query->get('matiereId');

        // Effectuer les opérations nécessaires pour obtenir les données mises à jour
        // en fonction de l'ID de la matière

        // Exemple de données pour la réponse JSON
        $matiere = $this->getDoctrine()->getRepository(Matieres::class)->find($matiereId);
        $totalMinutes = 0;
        $passedMinutes = 0;
        $teachers = [];

        foreach ($matiere->getEvent() as $event) {
            $totalMinutes += $event->getDuration();

            if ($event->getEnd() < new \DateTime()) {
                $passedMinutes += $event->getDuration();
            }

            if (!in_array($event->getTeacher(), $teachers)) {
                $teachers[] = $event->getTeacher();
            }
        }

        $totalHours = floor($totalMinutes / 60);
        $totalMinutes = $totalMinutes % 60;
        $passedHours = floor($passedMinutes / 60);
        $passedMinutes = $passedMinutes % 60;

        $averageNoteAll = 0;
        $averageNoteUser = 0;
        $notes = $matiere->getNotes();

        if (count($notes) > 0) {
            $totalNoteAll = 0;
            $totalNoteUser = 0;
            $countAll = 0;
            $countUser = 0;

            foreach ($notes as $note) {
                $totalNoteAll += $note->getNote() * $note->getCoefficient();
                $countAll += $note->getCoefficient();

                // Vérifier si la note appartient à l'utilisateur actuel
                if ($note->getUser() === $this->getUser()) {
                    $totalNoteUser += $note->getNote() * $note->getCoefficient();
                    $countUser += $note->getCoefficient();
                }
            }

            // Calculer la moyenne de toutes les notes
            $averageNoteAll = round($totalNoteAll / $countAll, 2);

            // Calculer la moyenne des notes de l'utilisateur actuel
            $averageNoteUser = round($totalNoteUser / $countUser, 2);
        }

        $response = [
            'matiere' => $matiere->getName(),
            'tempsTotal' => $totalHours . 'h ' . $totalMinutes . 'min',
            'tempsPasse' => $passedHours . 'h ' . $passedMinutes . 'min',
            'moyenneNotes' => $averageNoteAll . '/20',
            'moyenneNotesUtilisateur' => $averageNoteUser . '/20',
            'nomProfesseur' => implode(', ', $teachers),
        ];

        return new JsonResponse($response);
    }

}
