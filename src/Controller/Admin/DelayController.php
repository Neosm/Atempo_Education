<?php

namespace App\Controller\Admin;

use App\Entity\Delay;
use App\Entity\Event;
use App\Form\DelayType;
use App\Repository\DelayRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DelayController extends AbstractController
{
    #[Route('/admin/agenda/event/{eventId}/retard/create', name: 'admin_delay_create')]
    public function admincreate(Request $request, $eventId): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        $delay = new Delay();
        $delay->setEvent($event);

        $form = $this->createForm(DelayType::class, $delay, ['ecole' => $ecole]);
        $form->handleRequest($request);

        $ecole = $this->getUser()->getEcoles();
        if ($form->isSubmitted() && $form->isValid()) {
            $delay->setEcoles($ecole);
            $entityManager->persist($delay);
            $entityManager->flush();

            return $this->redirectToRoute('admin_agenda_event_details', ['id' => $eventId]);
        }

        return $this->render('admin/delay/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/agenda/event/{eventId}/retard/edit/{delayId}', name: 'admin_delay_edit')]
    public function adminedit(Request $request, $eventId, $delayId): Response
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

        return $this->render('admin/delay/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }







    #[Route('/admin/retard/delete/{delayId}', name: 'admin_liste_delay_delete')]
    public function admindelete($delayId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $delay = $entityManager->getRepository(Delay::class)->find($delayId);

        $entityManager->remove($delay);
        $entityManager->flush();

        return $this->redirectToRoute('admin_liste_delay_liste');
    }

    #[Route('/admin/retard/creer', name: 'admin_liste_delay_create')]
    public function admin_professeur_delay_create(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ecole = $this->getUser()->getEcoles();
        $ajouterChampEvent = true;
        $delay = new Delay();
        $user = $this->getUser();

        $form = $this->createForm(delayType::class, $delay, ['admin_creer_delay' => $ajouterChampEvent, 'userid' => $user->getId(), 'ecole' => $ecole]);
        $form->handleRequest($request);

        $ecole = $this->getUser()->getEcoles();
        if ($form->isSubmitted() && $form->isValid()) {
            $delay->setEcoles($ecole);


            $entityManager->persist($delay);
            $entityManager->flush();

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/delay/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/retard/liste-retards', name: 'admin_liste_delay_liste')]
    public function admin_professeur_delay_liste(DelayRepository $delayRepository, UsersRepository $usersRepository): Response
    {
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
    
        return $this->render('admin/delay/index.html.twig', [
            "delay" => $delays
        ]);
    }
    

    #[Route('/admin/delay/edit/{delayId}', name: 'admin_liste_delay_edit')]
    public function adminlisteedit(Request $request, $delayId, DelayRepository $delayRepository, UsersRepository $usersRepository): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $entityManager = $this->getDoctrine()->getManager();
        $delay = $delayRepository->find($delayId);
        $user = $usersRepository->findAllStudentByEcole($ecole);
        $ajouterChampEvent = true;

        $form = $this->createForm(delayType::class, $delay, ['admin_creer_delay' => $ajouterChampEvent, 'userid' => $user, 'ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($delay);
            $entityManager->flush();

            return $this->redirectToRoute('admin_liste_delay_liste');
        }

        return $this->render('admin/delay/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
