<?php

namespace App\Controller\Admin;

use App\Entity\StudentClass;
use App\Form\StudentClasseType;
use App\Repository\StudentClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentClasseController extends AbstractController
{
    #[Route('/admin/classe', name: 'admin_studentclasse_liste')]
    public function index(StudentClassRepository $studentClassRepository): Response
    {
        $studentClasses = $studentClassRepository->findAll();

        return $this->render('admin/student_classe/index.html.twig', [
            'studentClasses' => $studentClasses,
        ]);
    }

    #[Route('/admin/classe/{id}', name: 'admin_studentclasse_liste_details')]
    public function details(StudentClass $studentClass,StudentClassRepository $studentClassRepository): Response
    {
        $studentClasses = $studentClassRepository->findAll();

        return $this->render('admin/student_classe/index.html.twig', [
            'studentClass' => $studentClass,
            'studentClasses' => $studentClasses,
        ]);
    }

    #[Route('/admin/creation/classe', name: 'admin_studentclasse_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $studentClasses = new StudentClass;
        $form = $this->createForm(StudentClasseType::class, $studentClasses);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
    
            // Récupérer les élèves sélectionnés
            $selectedStudents = $form->get('students')->getData();
            
            // Enregistrer la classe avec les élèves
            $entityManager->persist($studentClasses);
    
            // Ajouter les élèves à la classe
            foreach ($selectedStudents as $student) {
                $student->setStudentClass($studentClasses);
                $entityManager->persist($student);
            }
            
            $entityManager->flush();
    
            $this->addFlash('success', 'Classe d\'élèves créée avec succès.');
    
            return $this->redirectToRoute('admin_studentclasse_liste');
        }
    
        return $this->render('admin/student_classe/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/classe/edit/{id}', name: 'admin_studentclasse_edit')]
    public function edit(Request $request, StudentClass $studentClass, ManagerRegistry $doctrine): Response
    {
        // Récupérer les élèves de la classe
        $studentsInClass = $studentClass->getStudents();
    
        // Effectuer une copie profonde des entités d'origine
        $originalStudents = clone $studentsInClass;
    
        // Créer le formulaire et pré-remplir les étudiants de la classe
        $form = $this->createForm(StudentClasseType::class, $studentClass, [
            'studentsInClass' => $studentsInClass,
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            // Récupérer les élèves sélectionnés
            $selectedStudents = $form->get('students')->getData();
            $entityManager = $doctrine->getManager();

    
            // Supprimer la StudentClass des utilisateurs qui ne sont plus dans la classe
            foreach ($originalStudents as $existingStudent) {
                if (!$selectedStudents->contains($existingStudent)) {
                    $existingStudent->setStudentClass(null);
                    $entityManager->persist($studentClass);
                }
            }
    
            // Ajouter les élèves à la classe
            foreach ($selectedStudents as $student) {
                $student->setStudentClass($studentClass);
                $entityManager->persist($student);
            }
            
            $entityManager->flush();

    
            $this->addFlash('success', 'Classe d\'élèves mise à jour avec succès.');
            return $this->redirectToRoute('admin_studentclasse_liste');
        }
    
        return $this->render('admin/student_classe/create.html.twig', [
            'form' => $form->createView(),
            'studentClass' => $studentClass,
        ]);
    }
    
    
    

    #[Route('/admin/classe/delete/{id}', name: 'admin_studentclasse_delete')]
    public function delete(StudentClass $studentClass, EntityManagerInterface $entityManager): Response
    {
        // 1. Retirer les références de la classe d'élèves des utilisateurs et événements liés
        foreach ($studentClass->getStudents() as $user) {
            $user->setStudentClass(null);
            $entityManager->persist($user);
        }
    
        foreach ($studentClass->getEvents() as $event) {
            $event->setStudentClass(null);
            $entityManager->persist($event);
        }
    
        // 2. Supprimer la classe d'élèves
        $entityManager->remove($studentClass);
        $entityManager->flush();
    
        $this->addFlash('success', 'Classe d\'élèves supprimée avec succès.');
    
        return $this->redirectToRoute('admin_studentclasse_liste');
    }

}
