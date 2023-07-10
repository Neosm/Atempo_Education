<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/agenda", name="admin_agenda_")
 */
class AgendaController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EventRepository $EventRepository): Response
    {
        $events = $EventRepository->findAll();
        $cours = [];

        foreach($events as $event){
            $cours[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
            ];
        }

        $data = json_encode($cours);

        return $this->render('admin/agenda/index.html.twig', [
            'dataEvents'=>$data,
        ]);
    }

    /**
     * @Route("/ajouter", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $Event = new Event();
        $form = $this->createForm(EventType::class, $Event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Event);
            $entityManager->flush();

            return $this->redirectToRoute('admin_agenda_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenda/new.html.twig', [
            'Event' => $Event,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Event $Event): Response
    {
        return $this->render('admin/agenda/show.html.twig', [
            'Event' => $Event,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Event $Event): Response
    {
        $form = $this->createForm(EventType::class, $Event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_agenda_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenda/edit.html.twig', [
            'Event' => $Event,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Event $Event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($Event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_agenda_home', [], Response::HTTP_SEE_OTHER);
    }



    /**
     * @Route("/api/{id}/edit", name="api_event_edit", methods={"PUT"})
     */
    public function majEvent(?Event $Event, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        if(
            property_exists($donnees, 'title') && $donnees->title !== null && !empty($donnees->title) &&
            (property_exists($donnees, 'start') && $donnees->start !== null) && !empty($donnees->start) &&
            (property_exists($donnees, 'description') && $donnees->description !== null) && !empty($donnees->description) &&
            (property_exists($donnees, 'backgroundColor') && $donnees->backgroundColor !== null) && !empty($donnees->backgroundColor) &&
            (property_exists($donnees, 'borderColor') && $donnees->borderColor !== null) && !empty($donnees->borderColor) &&
            (property_exists($donnees, 'textColor') && $donnees->textColor !== null) && !empty($donnees->textColor)
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if($Event === null){
                // On instancie un rendez-vous
                $Event = new Event;

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            $Event->setTitle($donnees->title);
            $Event->setDescription($donnees->description);
            $Event->setStart(new DateTime($donnees->start));
            if($donnees->allDay){
                $Event->setEnd(new DateTime($donnees->start));
            }else{
                $Event->setEnd(new DateTime($donnees->end));
            }
            $Event->setBackgroundColor($donnees->backgroundColor);
            $Event->setBorderColor($donnees->borderColor);
            $Event->setTextColor($donnees->textColor);

            $em = $this->getDoctrine()->getManager();
            $em->persist($Event);
            $em->flush();

            // On retourne le code
            return new Response('Ok', $code);
        }else{
            // Les données sont incomplètes
            return new Response('Données incomplètes', \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }


        return $this->render('api/index.html.twig');
    }
}
