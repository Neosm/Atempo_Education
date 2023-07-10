<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Entity\Event;
use App\Form\AbsenceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbsenceController extends AbstractController
{
    #[Route('/agenda/event/{eventId}/absence/create', name: 'absence_create')]
    public function create(Request $request, $eventId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        $absence = new absence();
        $absence->setEvent($event);
        $absence->setAbsenceDate($event->getStart());

        $form = $this->createForm(absenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('absence/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/agenda/event/{eventId}/absence/edit/{absenceId}', name: 'absence_edit')]
    public function edit(Request $request, $eventId, $absenceId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $absence = $entityManager->getRepository(absence::class)->find($absenceId);

        $form = $this->createForm(absenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('absence/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/agenda/event/{eventId}/absence/delete/{absenceId}', name: 'absence_delete')]
    public function delete($eventId, $absenceId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $absence = $entityManager->getRepository(absence::class)->find($absenceId);

        $entityManager->remove($absence);
        $entityManager->flush();

        return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
    }
}
