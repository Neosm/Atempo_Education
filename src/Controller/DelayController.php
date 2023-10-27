<?php

namespace App\Controller;

use App\Entity\Delay;
use App\Entity\Event;
use App\Form\DelayType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DelayController extends AbstractController
{
    #[Route('/agenda/event/{eventId}/retard/create', name: 'delay_create')]
    public function create(Request $request, $eventId): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        $delay = new Delay();
        $delay->setEvent($event);

        $form = $this->createForm(DelayType::class, $delay, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $delay->setEcoles($ecole);
            $entityManager->persist($delay);
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('delay/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/agenda/event/{eventId}/retard/edit/{delayId}', name: 'delay_edit')]
    public function edit(Request $request, $eventId, $delayId): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $entityManager = $this->getDoctrine()->getManager();
        $delay = $entityManager->getRepository(Delay::class)->find($delayId);

        $form = $this->createForm(DelayType::class, $delay, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('delay/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/retard/delete/{delayId}', name: 'professeur_delay_delete')]
    public function delete($delayId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $delay = $entityManager->getRepository(Delay::class)->find($delayId);

        $entityManager->remove($delay);
        $entityManager->flush();

        return $this->redirectToRoute('professeur_delay_liste');
    }

    #[Route('/retard/creer', name: 'professeur_delay_create')]
    public function professeur_delay_create(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ajouterChampEvent = true;
        $delay = new Delay();
        $user = $this->getUser();

        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(delayType::class, $delay, ['ajouter_creer_delay' => $ajouterChampEvent, 'userid' => $user->getId(), 'ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $delay->setEcoles($ecole);

            $entityManager->persist($delay);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('delay/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/retard/liste-retards', name: 'professeur_delay_liste')]
    public function professeur_delay_liste(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $delayRepository = $entityManager->getRepository(Delay::class);
    
        $user = $this->getUser();
        $ecoleId = $user->getEcoles()->getId();
    
        $delays = $delayRepository->createQueryBuilder('delay')
            ->join('delay.event', 'event') // Joindre la relation event
            ->join('event.ecoles', 'ecoles') // Joindre la relation ecoles
            ->where('ecoles.id = :ecoleId') // Filtrer les retards par l'ID de l'école
            ->setParameter('ecoleId', $ecoleId)
            ->orderBy('event.start', 'DESC') // Trier par la date de début de l'événement
            ->getQuery()
            ->getResult();
    

        return $this->render('delay/index.html.twig', [
            "delay" => $delays
        ]);
    }
}
