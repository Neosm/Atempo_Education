<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/agenda", name="admin_agenda_")
 */
class AgendaController extends AbstractController
{

    private $serializer;
    private $roomRepository;

    public function __construct(RoomRepository $roomRepository, SerializerInterface $serializer)
    {
        $this->roomRepository = $roomRepository;
        $this->serializer = $serializer;
    }


    function getDescription($event)
    {
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

        $description .= ' - Professeur: ' . $event->getTeacher()->getUserIdentifier();

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
        $events = $this->getDoctrine()->getRepository(Event::class)->findAll();

        // Convertir les événements en un format compatible avec FullCalendar (par exemple, JSON)
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'title' => $event->getTitle(),
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'teacher' => $event->getTeacher()->getUserIdentifier(),
                'description' => $this->getDescription($event),
                // Ajoutez ici d'autres champs que vous souhaitez afficher dans le calendrier
            ];
        }

        // Retourner les événements au format JSON
        $jsonEvents = json_encode($formattedEvents);

        return $this->render('admin/agenda/index.html.twig', [
            'jsonEvents' => $jsonEvents,
        ]);
    }

    /**
     * @Route("/get-event-details", name="get_event_details", methods={"POST"})
     */
    public function admingetEventDetails(Request $request): Response
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
        $lecons = $event->getLecons();
        $programmes = $event->getProgramme();

        if ($event->getStart() > new \DateTime & $event->getObjectif() == null) {
            $objectif = "Aucun objectif défini pour le prochain cours";
            $commentaire = null;
        } elseif ($event->getStart() > new \DateTime) {
            $objectif = $event->getObjectif();
            $commentaire = null;
        } elseif ($event->getStart() < new \DateTime &  $event->getComment() == null) {
            $commentaire = "Aucun commentaire défini pour le prochain cours";
            $objectif = null;
        } elseif ($event->getStart() < new \DateTime) {
            $commentaire = $event->getComment();
            $objectif = null;
        }



        // Convertir l'objet Event en tableau associatif
        $eventData = [
            'title' => $event->getTitle(),
            'id' => $event->getId(),
            'start' => $event->getStart()->format('Y-m-d H:i:s'),
            'end' => $event->getEnd()->format('Y-m-d H:i:s'),
            'room' => $event->getRoom() ? $event->getRoom()->getName() : null,
            'teacher' => $event->getTeacher()->getUserIdentifier(),
            'studentClass' => $studentClass ? $studentClass->getName() : null,
            'students' => $students->isEmpty() ? [] : $students->map(fn ($student) => $student->getUserIdentifier())->toArray(),
            'zoomlink' => $event->getZoomLink() ?? '',
            'lecons' => $lecons->isEmpty() ? [] : $lecons->map(fn ($lecons) => [
                'nom' => $lecons->getNom(),
                'slug' => $lecons->getSlug()
            ])->toArray(),
            'programmes' => $programmes->isEmpty() ? [] : $programmes->map(fn ($programme) => [
                'nom' => $programme->getNom(),
                'slug' => $programme->getSlug()
            ])->toArray(),
            'objectif' => $objectif,
            'commentaire' => $commentaire,
            // Ajoutez ici d'autres propriétés de l'événement que vous souhaitez inclure dans la réponse JSON
        ];

        // Renvoyer les informations de l'événement en tant que réponse JSON
        return new JsonResponse(['event' => $eventData]);
    }

    /**
     * @Route("/create", name="create_event")
     */
    public function admincreateEvent(Request $request): Response
    {
        $event = new Event();

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculer l'heure de fin en fonction de l'heure de début et de la durée
            $start = $form->get('start')->getData(); // Récupérer l'objet DateTime depuis le formulaire
            $event->setStart($start);

            $title = $form->get('matieres')->getData() . ' - ';
            $studentClass = $form->get('studentClass')->getData();
            $studentsData = $form->get('students')->getData();
            $numberOfStudents = count($studentsData);

            if ($studentClass) {
                $title .= $studentClass;
            } elseif ($numberOfStudents > 0) {
                $title .= $studentsData[0];

                if ($numberOfStudents > 1) {
                    $title .= ', ';

                    if ($numberOfStudents > 2) {
                        $title .= $studentsData[1] . ', ...';
                    } else {
                        $title .= $studentsData[1];
                    }
                }
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

            return $this->redirectToRoute('admin_agenda_home');
        }

        return $this->render('admin/agenda/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/{id}", name="event_details")
     */
    public function admineventDetails(Event $event): Response
    {
        return $this->render('admin/agenda/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/event/edit/{id}", name="edit_event")
     */
    public function admineditEvent(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculer l'heure de fin en fonction de l'heure de début et de la durée
            $start = $event->getStart();
            $duration = $event->getDuration();

            $title = $form->get('matieres')->getData() . ' - ';
            $studentClass = $form->get('studentClass')->getData();
            $studentsData = $form->get('students')->getData();
            $numberOfStudents = count($studentsData);

            if ($studentClass) {
                $title .= $studentClass;
            } elseif ($numberOfStudents > 0) {
                $title .= $studentsData[0];

                if ($numberOfStudents > 1) {
                    $title .= ', ';

                    if ($numberOfStudents > 2) {
                        $title .= $studentsData[1] . ', ...';
                    } else {
                        $title .= $studentsData[1];
                    }
                }
            }

            $event->setTitle($title);


            $end = clone $start;
            $end->modify("+$duration minutes");
            $event->setEnd($end);

            // Enregistrer les modifications de l'événement dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');

            return $this->redirectToRoute('admin_agenda_event_details', ['id' => $event->getId()]);
        }

        return $this->render('admin/agenda/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_event")
     */
    public function admindeleteEvent(Event $event): Response
    {
        // Supprimer l'événement de la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'L\'événement a été supprimé avec succès.');

        return $this->redirectToRoute('admin_agenda_home');
    }

    /**
     * @Route("/api/rooms", name="api_rooms", methods={"GET"})
     */
    public function adminapiRoomChange(Request $request, SerializerInterface $serializer)
    {

        $materialIds = $request->query->get('materials');
        $materialIdsArray = explode(',', $materialIds);
        $start = $request->query->get('start');
        // Récupérer les données envoyées via AJAX
        $zoomlink = $request->query->get('zoomlink');

        $duration = $request->query->get('duration'); // Récupérer la durée depuis la requête
        $startTime = new \DateTime($start);
        $end = clone $startTime;
        $end->add(new \DateInterval('PT' . intval($duration) . 'M')); // Ajouter la durée en minutes

        // Formatter endTime en tant que chaîne de caractères au format souhaité
        $endAsString = $end->format('Y-m-d H:i');

        // Récupérer les salles réservées en fonction de l'heure de début et de fin
        $reservedRooms = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findReservedRooms($start, $endAsString);


        // Récupérer l'ID de l'événement que vous modifiez (si applicable)
        $eventId = $request->query->get('eventId'); // Remplacez 'eventId' par la clé réelle

        if ($eventId) {
            // Récupérer l'événement en fonction de son ID
            $event = $this->getDoctrine()
                ->getRepository(Event::class)
                ->find($eventId);

            // Si l'événement existe et a une salle réservée, retirer cette salle des salles réservées
            if ($event && $event->getRoom()) {
                $eventRoomId = $event->getRoom()->getId();
                $reservedRooms = array_filter($reservedRooms, function ($reservedRoom) use ($eventRoomId) {
                    return $reservedRoom->getId() !== $eventRoomId;
                });
            }
        }
        dump($zoomlink);
        if (!empty($zoomlink)) {
            // Si le champ "zoomlink" est rempli, aucune salle ne sera affichée
            $availableRooms = [];
        } else {
            if (empty(array_filter($materialIdsArray))) {
                // Si aucun équipement n'est sélectionné, renvoyer toutes les salles
                $filteredRooms = $this->roomRepository->findAll();
            } else {
                // Créer le QueryBuilder filtré pour les matériaux sélectionnés
                $queryBuilder = $this->roomRepository->createFilteredQuery($materialIdsArray);
                // Exécuter la requête et récupérer les salles en fonction des équipements sélectionnés
                $filteredRooms = $queryBuilder->getQuery()->getResult();
            }
        
            // Filtrer les salles disponibles en excluant les salles réservées
            $availableRooms = array_filter($filteredRooms, function ($room) use ($reservedRooms) {
                return !in_array($room, $reservedRooms);
            });
        
            $availableRooms = array_values($availableRooms); // Réindexer le tableau
        }

        dump($availableRooms);
        $roomData = [];
        foreach ($availableRooms as $room) {
            $roomData[] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
            ];
        }

        $normalizedRooms = $serializer->normalize($roomData, null, [
            'circular_reference_handler' => function ($object) {
                return $object['id'];
            }
        ]);

        $response = new JsonResponse($normalizedRooms);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);

        // Renvoyer la réponse
        return $response;
    }
}
