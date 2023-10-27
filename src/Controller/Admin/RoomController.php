<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Materials;
use App\Entity\Room;
use App\Form\EquipementType;
use App\Form\RoomType;
use App\Repository\MaterialsRepository;
use App\Repository\RoomRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    #[Route('/admin/room', name: 'admin_salle_liste')]
    public function index(RoomRepository $roomRepository): Response
    {
        
        $ecole = $this->getUser()->getEcoles();
        $rooms = $roomRepository->findBy(["ecoles" => $ecole]);

        return $this->render('admin/room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/admin/salle/create', name: 'admin_salle_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $room = new Room();
        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(RoomType::class, $room, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $room->setEcoles($ecole);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            $this->addFlash('success', 'Salle créée avec succès.');

            return $this->redirectToRoute('admin_salle_liste');
        }

        return $this->render('admin/room/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/salle/edit/{id}', name: 'admin_salle_edit')]
    public function edit(Request $request, Room $room, ManagerRegistry $doctrine): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(RoomType::class, $room, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Salle mise à jour avec succès.');

            return $this->redirectToRoute('admin_salle_liste');
        }

        return $this->render('admin/room/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/salle/delete/{id}', name: 'admin_salle_delete')]
    public function delete(Room $room, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
    
        // Récupérer les événements associés à la salle
        $eventsWithRoom = $room->getEvents();
    
        // Supprimer la salle de chaque événement associé
        foreach ($eventsWithRoom as $event) {
            $event->setRoom(null);
            $entityManager->persist($event);
        }
    
        // Supprimer la salle elle-même
        $entityManager->remove($room);
        $entityManager->flush();
    
        $this->addFlash('success', 'Salle supprimée avec succès.');
    
        return $this->redirectToRoute('admin_salle_liste');
    }

    #[Route('/admin/salle/load-events', name: 'load_room_events')]
    public function loadRoomEvents(Request $request): JsonResponse
    {
        $roomId = $request->query->get('roomId');
        $entityManager = $this->getDoctrine()->getManager();

        // Récupérer les événements liés à la salle
        $roomEvents = $entityManager->getRepository(Event::class)->findBy(['room' => $roomId]);

        $serializedEvents = [];
        foreach ($roomEvents as $event) {
            // Sérialisez les informations nécessaires de chaque événement
            $serializedEvents[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                // Ajoutez d'autres informations si nécessaire
            ];
        }

        return new JsonResponse(['events' => $serializedEvents]);
    }
    

    #[Route('/admin/salle/details/{id}', name: 'admin_salle_liste_details')]
    public function details(Room $room, RoomRepository $roomRepository): Response
    {
        
        $ecole = $this->getUser()->getEcoles();
        $rooms = $roomRepository->findBy(["ecoles" => $ecole]);

        return $this->render('admin/room/index.html.twig', [
            'room' => $room,
            'rooms' => $rooms,
        ]);
    }



    
    #[Route('/admin/salle/equipement', name: 'admin_salle_equipement_liste')]
    public function equipement(MaterialsRepository $materialRepository): Response
    {
        
        $ecole = $this->getUser()->getEcoles();
        $materials = $materialRepository->findBy(["ecoles" => $ecole]);
    
        return $this->render('admin/room/equipement/index.html.twig', [
            'materials' => $materials,
        ]);
    }

    #[Route('/admin/salle/equipement/creer', name: 'admin_salle_equipement_create')]
    public function equipementCreer(Request $request, ManagerRegistry $doctrine): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $material = new Materials();
        $form = $this->createForm(EquipementType::class, $material, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();

            $selectedRooms = $form->get('rooms')->getData();
            $material->setEcoles($ecole);
            $entityManager->persist($material);


            foreach ($selectedRooms as $room) {
                $room->addMaterial($material);
                $entityManager->persist($room);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Équipement créé avec succès.');
            return $this->redirectToRoute('admin_salle_equipement'); 
        }
        return $this->render('admin/room/equipement/new.html.twig', ['form' => $form->createView(),]);
    }
    
    
    #[Route('/admin/salle/equipement/modifier/{id}', name: 'admin_salle_equipement_edit')]
    public function equipementEdit(Request $request, Materials $material, ManagerRegistry $doctrine): Response
    {
        $ecole = $this->getUser()->getEcoles();
        // Récupérer les salles associées à l'équipement
        $roomsWithMaterial = $material->getRooms();
        
        // Effectuer une copie profonde des entités d'origine
        $originalRooms = clone $roomsWithMaterial;
        
        // Créer le formulaire et pré-remplir les salles associées à l'équipement
        $form = $this->createForm(EquipementType::class, $material, ['ecole' => $ecole]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les salles sélectionnées
            $selectedRooms = $form->get('rooms')->getData();
            $entityManager = $doctrine->getManager();
        
            // Supprimer l'équipement des salles qui ne sont plus associées
            foreach ($originalRooms as $existingRoom) {
                if (!$selectedRooms->contains($existingRoom)) {
                    $existingRoom->removeMaterial($material);
                    $entityManager->persist($existingRoom);
                }
            }
        
            // Ajouter l'équipement aux salles
            foreach ($selectedRooms as $room) {
                $room->addMaterial($material);
                $entityManager->persist($room);
            }
            
            $entityManager->flush();
        
            $this->addFlash('success', 'Équipement mis à jour avec succès.');

            return $this->redirectToRoute('admin_salle_equipement_liste');
        }
        
        return $this->render('admin/room/equipement/new.html.twig', [
            'form' => $form->createView(),
            'material' => $material,
        ]);
    }
    
    #[Route('/admin/salle/equipement/supprimer/{id}', name: 'admin_salle_equipement_delete')]
    public function equipementSupprimer(Materials $material, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($material);
        $entityManager->flush();
    
        $this->addFlash('success', 'Équipement supprimé avec succès.');
    
        return $this->redirectToRoute('admin_salle_equipement_liste');
    }
    
    #[Route('/admin/salle/equipement/details/{id}', name: 'admin_salle_equipement_liste_details')]
    public function equipementDetails(Materials $material, MaterialsRepository $materialRepository): Response
    {
        
        $ecole = $this->getUser()->getEcoles();
        $materials = $materialRepository->findBy(["ecoles" => $ecole]);

        return $this->render('admin/room/equipement/index.html.twig', [
            'material' => $material,
            'materials' => $materials,
        ]);
    }
}
