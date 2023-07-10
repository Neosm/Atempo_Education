<?php

namespace App\Controller\Admin;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
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

        return $this->render('admin/index.html.twig', [
            'dataEvents'=>$jsonEvents,
        ]);
    }
}
