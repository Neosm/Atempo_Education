<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatièresController extends AbstractController
{
    #[Route('/matieres', name: 'app_matieres')]
    public function index(): Response
    {
        // Récupérer l'utilisateur actuel
        $user = $this->getUser();
        
        // Récupérer la studentClass de l'utilisateur
        $studentClass = $user->getStudentClass();
        
        // Récupérer tous les événements liés à l'utilisateur ou à la studentClass
        $events = $user->getEvents()->toArray();
        if ($studentClass) {
            $events = array_merge($events, $studentClass->getEvents()->toArray());
        }
        
        // Récupérer toutes les matières liées aux événements
        $matieres = [];
        foreach ($events as $event) {
            $matiere = $event->getMatieres();
            if (!in_array($matiere, $matieres)) {
                $matieres[] = $matiere;
            }
        }
        return $this->render('matières/index.html.twig', [
            'matieres' => $matieres,
        ]);
    }
}
