<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Matieres;
use App\Entity\Notes;
use App\Form\MatieresType;
use App\Repository\MatieresRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatieresController extends AbstractController
{
    #[Route('/admin/matieres', name: 'admin_matiere_liste')]
    public function index(MatieresRepository $matieresRepository): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $matieres = $matieresRepository->findBy(["ecoles" => $ecole]);

        return $this->render('admin/matieres/index.html.twig', [
            'matieres' => $matieres,
        ]);
    }

    #[Route('/admin/matieres/create', name: 'admin_matiere_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $matiere = new Matieres();
        $form = $this->createForm(MatieresType::class, $matiere);
        
        $form->handleRequest($request);
        
        $ecole = $this->getUser()->getEcoles();
        if ($form->isSubmitted() && $form->isValid()) {
            $matiere->setEcoles($ecole);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($matiere);
            $entityManager->flush();

            $this->addFlash('success', 'Discipline créée avec succès.');
            
            return $this->redirectToRoute('admin_matiere_liste');
        }

        return $this->render('admin/matieres/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/matieres/edit/{id}', name: 'admin_matiere_edit')]
    public function edit(Request $request, Matieres $matiere, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(MatieresType::class, $matiere);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Discipline mise à jour avec succès.');
            
            return $this->redirectToRoute('admin_matiere_liste');
        }

        return $this->render('admin/matieres/new.html.twig', [
            'form' => $form->createView(),
            'matiere' => $matiere,
        ]);
    }

    #[Route('/admin/matieres/delete/{id}', name: 'admin_matiere_delete')]
    public function delete(Matieres $matiere, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        // Suppression de la Discipline des entités associées (Notes et Event)
        $notesRepository = $doctrine->getRepository(Notes::class);
        $eventsRepository = $doctrine->getRepository(Event::class);

        # Suppression des notes associées à la Discipline
        $notes = $notesRepository->findBy(['matiere' => $matiere]);
        foreach ($notes as $note) {
            if ($note->getMatiere() === $matiere) {
                $note->setMatiere(null);
                $entityManager->persist($note);
            }
        }

        # Suppression des events associés à la Discipline
        $events = $eventsRepository->findBy(['matieres' => $matiere]);
        foreach ($events as $event) {
            if ($event->getMatieres() === $matiere) {
                $event->setMatieres(null);
                $entityManager->persist($event);
            }
        }

        # Enfin, supprimez la Discipline elle-même
        $entityManager->remove($matiere);
        $entityManager->flush();


        // Suppression de la Discipline elle-même
        $entityManager->remove($matiere);
        $entityManager->flush();

        $this->addFlash('success', 'Discipline supprimée avec succès.');

        return $this->redirectToRoute('admin_matiere_liste');
    }
}
