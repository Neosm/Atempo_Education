<?php

namespace App\Controller\Admin;

use App\Entity\Notes;
use App\Form\NoteType;
use App\Repository\NotesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatsController extends AbstractController
{

    #[Route('/admin/resultat', name: 'admin_resultats_liste')]
    public function index(NotesRepository $notesRepository): Response
    {
        
        $ecole = $this->getUser()->getEcoles();
        $notes = $notesRepository->findBy(["ecoles" => $ecole]);

        // Rendu de la vue Twig avec les résultats
        return $this->render('admin/resultat/index.html.twig', [
            'notes' => $notes
        ]);
    }

    #[Route('/admin/resultat/ajouter', name: 'admin_resultats_ajouter')]
    public function add(Request $request): Response
    {
        $note = new Notes();
        $ecole = $this->getUser()->getEcoles();

        $form = $this->createForm(NoteType::class, $note, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setEcoles($ecole);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'La note a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_resultats_liste');
        }


        return $this->render('admin/resultat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/resultat/modifier/{id}', name: 'admin_resultats_edit')]
    public function modifier(Request $request, Notes $note): Response
    {
        $ecole = $this->getUser()->getEcoles();

        $form = $this->createForm(NoteType::class, $note, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'La note a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_resultats_liste');
        }


        return $this->render('admin/resultat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/resultat/supprimer/{id}', name: 'admin_resultats_supprimer')]
    public function delete(Notes $note, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        // Supprimer la note de la base de données
        $entityManager->remove($note);
        $entityManager->flush();
        
        $this->addFlash('success', 'Note supprimée avec succès.');
        
        return $this->redirectToRoute('admin_resultats_liste');
    }
}
