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
    #[Route('/agenda/event/{eventId}/delay/create', name: 'delay_create')]
    public function create(Request $request, $eventId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        $delay = new Delay();
        $delay->setEvent($event);

        $form = $this->createForm(DelayType::class, $delay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($delay);
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('delay/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/agenda/event/{eventId}/delay/edit/{delayId}', name: 'delay_edit')]
    public function edit(Request $request, $eventId, $delayId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $delay = $entityManager->getRepository(Delay::class)->find($delayId);

        $form = $this->createForm(DelayType::class, $delay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('delay/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/agenda/event/{eventId}/delay/delete/{delayId}', name: 'delay_delete')]
    public function delete($eventId, $delayId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $delay = $entityManager->getRepository(Delay::class)->find($delayId);

        $entityManager->remove($delay);
        $entityManager->flush();

        return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
    }
}
