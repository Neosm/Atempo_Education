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
    #[Route('/agenda/event/{eventId}/absence/create', name: 'absence_event_create')]
    public function create(Request $request, $eventId): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        $absence = new absence();
        $absence->setEvent($event);
        $absence->setAbsenceDate($event->getStart());

        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(absenceType::class, $absence, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $absence->setEcoles($ecole);
            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('absence/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/agenda/event/{eventId}/absence/edit/{absenceId}', name: 'absence_event_edit')]
    public function edit(Request $request, $eventId, $absenceId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $absence = $entityManager->getRepository(absence::class)->find($absenceId);

        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(absenceType::class, $absence, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('absence/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/absences/supprimer/{absenceId}', name: 'professeur_absence_delete')]
    public function delete($absenceId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $absence = $entityManager->getRepository(absence::class)->find($absenceId);

        $entityManager->remove($absence);
        $entityManager->flush();

        return $this->redirectToRoute('professeur_absence_liste');
    }

    #[Route('/absences/creer', name: 'professeur_absence_create')]
    public function professeur_absence_create(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ajouterChampEvent = true;
        $absence = new absence();
        $user = $this->getUser();
        
        $ecole = $user->getEcoles();

        $form = $this->createForm(absenceType::class, $absence, ['creer_absence_professeur' => $ajouterChampEvent, 'userid' => $user->getId(), 'ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = $form->get('event')->getData();
            $absence->setAbsenceDate($event->getStart());
            $absence->setEcoles($ecole);

            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('absence/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/absences/liste-absences', name: 'professeur_absence_liste')]
    public function professeur_absence_liste(Request $request): Response
    {
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $absenceRepository = $entityManager->getRepository(Absence::class);
        $ecoleId = $user->getEcoles()->getId();
        // Récupérer toutes les absences avec un événement dont le teacher id correspond à l'ID de l'utilisateur
        $absences = $absenceRepository->createQueryBuilder('a')
            ->leftJoin('a.event', 'e')
            ->join('event.ecoles', 'ecoles')
            ->where('ecoles.id = :ecoleId')
            ->andWhere('e.teacher = :teacherId')
            ->setParameter('teacherId', $user->getId())
            ->setParameter('ecoleId', $ecoleId)
            ->getQuery()
            ->getResult();

        return $this->render('absence/index.html.twig', [
            "absence" => $absences
        ]);
    }
    
}
