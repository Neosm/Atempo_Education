<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Room;
use App\Form\EventFormType;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

    /**
     * @Route("/agenda", name="agenda_")
    * @package App\Controller
    */

class EventController extends AbstractController
{

    private $serializer;
    private $roomRepository;

    public function __construct(RoomRepository $roomRepository, SerializerInterface $serializer)
    {
        $this->roomRepository = $roomRepository;
        $this->serializer = $serializer;
    }


    function getDescription($event) {
        $students = $event->getStudents();
        $description = '';

        if ($event->getStudentClass()) {
            $description .= $event->getStudentClass()->getName();
        } else {
            foreach ($students as $student) {
                $description .= $student->getName() . ', ';
            }
            $description = rtrim($description, ', '); // Supprimer la virgule finale
        }

        $description .= ' - Professeur: ' . $event->getTeacher()->getUsername();

        return $description;
    }



    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        // Récupérer l'utilisateur connecté (vous pouvez adapter cela selon votre logique d'authentification)
        $user = $this->getUser();

        // Récupérer les événements associés à l'utilisateur
        $events = $this->getDoctrine()->getRepository(Event::class)->findEventsByUser($user);

        // Convertir les événements en un format compatible avec FullCalendar (par exemple, JSON)
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [ 
                'title' => $event->getTitle(),
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'room' => $event->getRoom()->getName(),
                'teacher' => $event->getTeacher()->getUsername(),
                'description' => $this->getDescription($event),
                // Ajoutez ici d'autres champs que vous souhaitez afficher dans le calendrier
            ];
        }

        // Retourner les événements au format JSON
        $jsonEvents = json_encode($formattedEvents);

        return $this->render('agenda/index.html.twig', [
            'jsonEvents' => $jsonEvents,
        ]);
    }

    /**
     * @Route("/get-event-details", name="get_event_details", methods={"POST"})
     */
    public function getEventDetails(Request $request): Response
    {
        $eventId = $request->request->get('eventId');

        // Récupérer les informations de l'événement en fonction de son ID
        $event = $this->getDoctrine()->getRepository(Event::class)->find($eventId);
    
        // Vérifier si l'événement existe
        if (!$event) {
            return new JsonResponse(['error' => 'L\'événement n\'a pas été trouvé.'], 404);
        }
    
        $studentClass = $event->getStudentClass();
        $studentClassData = $studentClass ? $studentClass->getName() : '';     

        $studentClass = $event->getStudentClass();
        $students = $event->getStudents();

        // Convertir l'objet Event en tableau associatif
        $eventData = [
            'title' => $event->getTitle(),
            'id' => $event->getId(),
            'start' => $event->getStart()->format('Y-m-d H:i:s'),
            'end' => $event->getEnd()->format('Y-m-d H:i:s'),
            'room' => $event->getRoom()->getName(),
            'teacher' => $event->getTeacher()->getUsername(),
            'studentClass' => $studentClass ? $studentClass->getName() : null,
            'students' => $students->isEmpty() ? [] : $students->map(fn($student) => $student->getUsername())->toArray(),
            // Ajoutez ici d'autres propriétés de l'événement que vous souhaitez inclure dans la réponse JSON
        ];

        // Renvoyer les informations de l'événement en tant que réponse JSON
        return new JsonResponse(['event' => $eventData]);
    }

    /**
     * @Route("/create", name="create_event")
     */
    public function createEvent(Request $request): Response
    {
        $event = new Event();

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculer l'heure de fin en fonction de l'heure de début et de la durée
            $start = $form->get('start')->getData(); // Récupérer l'objet DateTime depuis le formulaire
            $event->setStart($start);

            $title = $form->get('matieres')->getData() . ' - ';
            if ($form->get('studentClass')->getData()) {
                $title .= $form->get('studentClass')->getData();
            } else {
                foreach ($form->get('students')->getData() as $student) {
                    $title .= $student . ', ';
                }
                // On retire la virgule supplémentaire
                $title = rtrim($title, ", ");
            }

            $event->setTitle($title);

            $duration = $event->getDuration();
            $event->setIdUnique(uniqid(mt_rand(), true));
        
            $end = clone $start;
            $end->modify("+$duration minutes");
            $event->setEnd($end);

            // Enregistrer l'événement dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès.');

            return $this->redirectToRoute('agenda_home');
        }

        return $this->render('agenda/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/{id}", name="event_details")
     */
    public function eventDetails(Event $event): Response
    {
        return $this->render('agenda/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/event/edit/{id}", name="edit_event")
     */
    public function editEvent(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculer l'heure de fin en fonction de l'heure de début et de la durée
            $start = $event->getStart();
            $duration = $event->getDuration();

            $title = $form->get('matieres')->getData() . ' - ';
            if ($form->get('studentClass')->getData()) {
                $title .= $form->get('studentClass')->getData();
            } else {
                foreach ($form->get('students')->getData() as $student) {
                    $title .= $student . ', ';
                }
                // On retire la virgule supplémentaire
                $title = rtrim($title, ", ");
            }

            $event->setTitle($title);

        
            $end = clone $start;
            $end->modify("+$duration minutes");
            $event->setEnd($end);

            // Enregistrer les modifications de l'événement dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');

            return $this->redirectToRoute('agenda_event_details', ['id' => $event->getId()]);
        }

        return $this->render('agenda/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_event")
     */
    public function deleteEvent(Event $event): Response
    {
        // Supprimer l'événement de la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'L\'événement a été supprimé avec succès.');

        return $this->redirectToRoute('agenda_home');
    }

    /**
     * @Route("/export/event/{id}", name="export_event")
     */
    public function exportEvent(Event $event): Response
    {
        $user = $this->getUser();
        // Générer le contenu du fichier ICal
        $content = "BEGIN:VCALENDAR\r\n";
        $content .= "VERSION:2.0\r\n";
        $content .= "PRODID;LANGUAGE=fr:Copyright Atempo-Education @date du jour\r\n";
        $content .= "BEGIN:VEVENT\r\n";
        $content .= "UID:" . $event->getidUnique() . " " . $user . "\r\n";
        $content .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $content .= "DTSTART:" . $event->getStart()->format('Ymd\THis\Z') . "\r\n";
        $content .= "DTEND:" . $event->getEnd()->format('Ymd\THis\Z') . "\r\n";
        $content .= "LOCATION:" . $event->getRoom()->getName() . "\r\n";

        $description = "";

        if ($event->getStudentClass()) {
            $description .= "Classe: " . $event->getStudentClass()->getName() . "\\n";
        } elseif ($event->getStudents()->count() > 0) {
            $description .= "Élèves: ";

            foreach ($event->getStudents() as $student) {
                $description .= $student->getName() . ", ";
            }

            // Supprimer la virgule finale
            $description = rtrim($description, ", ") . "\\n";
        }

        $description .= "Professeur: " . $event->getTeacher()->getUsername() . "\\n";
        $description .= "Salle: " . $event->getRoom()->getName();

        $content .= "SUMMARY:" . $event->getTitle() . "\r\n";
        $content .= "DESCRIPTION:" . $description . "\r\n";
        $content .= "END:VEVENT\r\n";
        $content .= "END:VCALENDAR";

        // Envoi du fichier ICal en tant que réponse
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="event.ics"');

        return $response;
    }

    /**
     * @Route("/export/download", name="export_calendar_download")
     */
    public function exportEvents(): Response
    {
        $user = $this->getUser();

        // Récupérer les événements associés à l'utilisateur
        $events = $this->getDoctrine()->getRepository(Event::class)->findEventsByUser($user);

        // Calculer la date du premier et dernier événement
        $firstEvent = reset($events);
        $lastEvent = end($events);
        $startDate = $firstEvent->getStart()->format('Ymd');
        $endDate = $lastEvent->getEnd()->format('Ymd');
        $userDateOfBirth = $user->getDateOfBirth()->format('Y-m-d');


        // Générer le contenu du fichier iCalendar
        $content = "BEGIN:VCALENDAR\r\n";
        $content .= "VERSION:2.0\r\n";
        $content .= "PRODID;LANGUAGE=fr:Copyright ATempo-Education @" . date('Y') . "\r\n";
        $content .= "X-CALSTART:" . $startDate . "\r\n";
        $content .= "X-CALEND:" . $endDate . "\r\n";
        $content .= "X-WR-CALNAME;LANGUAGE=fr:ATempo - " . $user->getLastName() . " " . $user->getFirstName() . " - " . $userDateOfBirth . " - " . $user->getStudentClass()->getName() . "\r\n";
        $content .= "X-WR-CALDESC;LANGUAGE=fr:Emploi du temps " . $user->getLastName() . " " . $user->getFirstName() . " - " . $userDateOfBirth . " - " . $user->getStudentClass()->getName() . " | Généré par ATempo.Education - Semaines :"  . date('W') . " - " . $lastEvent->getEnd()->format('W') . "\r\n";

        foreach ($events as $event) {
            $content .= "BEGIN:VEVENT\r\n";
            $content .= "CATEGORIES:ATempo.Education\r\n";
            $content .= "UID:" . $event->getidUnique() . " " . $user . "\r\n";
            $content .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            $content .= "DTSTART:" . $event->getStart()->format('Ymd\THis\Z') . "\r\n";
            $content .= "DTEND:" . $event->getEnd()->format('Ymd\THis\Z') . "\r\n";
            $content .= "LOCATION:" . $event->getRoom()->getName() . "\r\n";

            $description = "";

            if ($event->getStudentClass()) {
                $description .= "Classe: " . $event->getStudentClass()->getName() . "\\n";
            } elseif ($event->getStudents()->count() > 0) {
                $description .= "Élèves: ";

                foreach ($event->getStudents() as $student) {
                    $description .= $student->getName() . ", ";
                }

                // Supprimer la virgule finale
                $description = rtrim($description, ", ") . "\\n";
            }

            $description .= "Professeur: " .$event->getTeacher() . "\\n";
            $description .= "Salle: " . $event->getRoom()->getName();

            $content .= "SUMMARY:" .  $event->getTitle() . " - " .$event->getTeacher() . "\r\n";
            $content .= "DESCRIPTION:" . $description . "\r\n";
            $content .= "END:VEVENT\r\n";
        }

        $content .= "END:VCALENDAR";

        // Générer le nom du fichier
        $filename = 'edt_' . $user . '.ics';

        // Envoi du fichier iCalendar en tant que réponse
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * @Route("/api/rooms", name="api_rooms", methods={"GET"})
     */
    public function apiRoomChange(Request $request): JsonResponse
    {
        $materialIds = $request->query->get('materials');
        $materialIdsArray = explode(',', $materialIds);
        $start = $request->query->get('start');

        // Vérifier si aucune option n'est sélectionnée
        if (empty(array_filter($materialIdsArray))) {
            // Récupérer toutes les salles à partir du repository
            $rooms = $this->roomRepository->findAll();
        } else {
            // Votre logique pour récupérer les salles en fonction des matériaux sélectionnés
            $rooms = $this->roomRepository->findRoomsByMaterials($materialIdsArray);
        }

        // Filtrer les salles disponibles en fonction de l'horaire
        if (!empty($start)) {
            // Effectuez ici la logique pour récupérer les salles réservées
            // en fonction de l'heure de début

            // Exemple de code pour récupérer les salles réservées
            $reservedRooms = $this->getDoctrine()
                ->getRepository(Event::class)
                ->findReservedRooms($start);

            // Filtrer les salles disponibles en excluant les salles réservées
            $availableRooms = [];
            foreach ($rooms as $room) {
                if (!in_array($room, $reservedRooms)) {
                    $availableRooms[] = $room;
                }
            }

            $rooms = $availableRooms;
        }

        // Convertir les salles en tableau JSON et renvoyer la réponse
        $response = new JsonResponse(
            $this->serializer->normalize($rooms, null, [AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }])
        );
        // Supprimer les informations supplémentaires de la réponse
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);

        // Renvoyer la réponse
        return $response;
    }


    /**
     * @Route("/api/reserved_rooms", name="api_reserved_rooms", methods={"GET"})
     */
    public function getReservedRooms(Request $request): JsonResponse
    {
        $start = $request->query->get('start');

        // Effectuez ici la logique pour récupérer les salles réservées
        // en fonction de l'heure de début

        // Exemple de code pour récupérer les salles réservées
        $reservedRooms = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findReservedRooms($start);

        // Convertir les salles réservées en tableau d'identifiants
        $reservedRoomIds = [];
        foreach ($reservedRooms as $room) {
            $reservedRoomIds[] = $room->getId();
        }

        // Renvoyer les salles réservées en tant que réponse JSON
        return new JsonResponse([
            'reservedRooms' => $reservedRoomIds,
        ]);
    }




}
