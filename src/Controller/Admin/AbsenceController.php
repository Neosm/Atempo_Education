<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use App\Entity\Event;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbsenceController extends AbstractController
{
    #[Route('/admin/agenda/event/{eventId}/absence/create', name: 'admin_absence_event_create')]
    public function admincreate(Request $request, $eventId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        $ecole = $this->getUser()->getEcoles();

        $absence = new absence();
        $absence->setEvent($event);
        $absence->setAbsenceDate($event->getStart());
        $absence->setEcoles($ecole);

        $ecole = $this->getUser()->getEcoles();

        $form = $this->createForm(absenceType::class, $absence, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('admin/absence/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/agenda/event/{eventId}/absence/edit/{absenceId}', name: 'admin_absence_event_edit')]
    public function adminedit(Request $request, $eventId, $absenceId): Response
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

        return $this->render('admin/absence/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/absences/supprimer/{absenceId}', name: 'admin_liste_absence_delete')]
    public function admindelete($absenceId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $absence = $entityManager->getRepository(absence::class)->find($absenceId);

        $entityManager->remove($absence);
        $entityManager->flush();

        return $this->redirectToRoute('admin_liste_absence_liste');
    }

    #[Route('/admin/absences/creer', name: 'admin_liste_absence_create')]
    public function admin_professeur_absence_create(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ajouterChampEvent = true;
        $absence = new absence();
        $ecole = $this->getUser()->getEcoles();

        $form = $this->createForm(absenceType::class, $absence, ['creer_absence_admin' => $ajouterChampEvent, 'ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = $form->get('event')->getData();
            $absence->setAbsenceDate($event->getStart());
            $absence->setEcoles($ecole);

            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/absence/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/absences/liste-absences', name: 'admin_liste_absence_liste')]
    public function admin_professeur_absence_liste(AbsenceRepository $absenceRepository): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $absences = $absenceRepository->findBy(['ecoles' => $ecole]);

        return $this->render('admin/absence/index.html.twig', [
            "absence" => $absences
        ]);
    }

    #[Route('/admin/absence/edit/{absenceId}', name: 'admin_liste_absence_edit')]
    public function adminlisteedit(Request $request, $absenceId, AbsenceRepository $absenceRepository, UsersRepository $usersRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $absence = $absenceRepository->find($absenceId);
        $ecole = $this->getUser()->getEcoles();
        $user = $usersRepository->findAllStudentByEcole($ecole);
        $ajouterChampEvent = true;


        $form = $this->createForm(absenceType::class, $absence, ['creer_absence_admin' => $ajouterChampEvent, 'userid' => $user->getId(), 'ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->get('event')->getData();
            $absence->setAbsenceDate($event->getStart());
            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('admin_liste_absence_liste');
        }

        return $this->render('admin/absence/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
