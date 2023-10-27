<?php

namespace App\Controller\Admin;

use App\Entity\Programmes;
use App\Entity\Users;
use App\Form\EditProgrammesType;
use App\Form\ProgrammesType;
use App\Repository\LeconsRepository;
use App\Repository\ProgrammesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route("/admin/programmes", name="admin_programmes_")
 */
class ProgrammesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProgrammesRepository $programmesRepository): Response
    {
        
        $ecole = $this->getUser()->getEcoles();
        $allprogrammes = $programmesRepository->findBy(["ecoles" => $ecole]);

        return $this->render('admin/programmes/index.html.twig', [
            'programmes'=>$allprogrammes,
        ]);
    }

    /**
     * @Route("/ajouter", name="ajouter")
     */
    public function ajouter(Request $request, SluggerInterface $slugger): Response
    {
        $programme = new Programmes();
        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(ProgrammesType::class, $programme, [
            'ecole' => $ecole,
        ]);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            $programme->setEcoles($ecole);
            $programme->setSlug($slugger->slug($form->get('nom')->getData()));
            $programme->addUser($this->getUser());
            $images = $form->get('image')->getData();
            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $images->guessExtension();

                try {
                    $images->move(
                        $this->getParameter('lecons_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $programme->setImage($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            $em->flush();

            return $this->redirectToRoute('admin_programmes_home');
        }


        return $this->render('admin/programmes/ajout.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="edit")
     */
    public function modifierProgramme(Request $request, Programmes $programme, SluggerInterface $slugger): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(EditProgrammesType::class, $programme, [
            'ecole' => $ecole,
        ]);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('image')->getData();
            $programme->setSlug($slugger->slug($form->get('nom')->getData()));
            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $images->guessExtension();

                try {
                    $images->move(
                        $this->getParameter('lecons_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $programme->setImage($newFilename);
            }
            if($programme->getProgrammesLecons()->getId() == 1) {
                $getUsers = $form->get('users')->getData();
                foreach($getUsers as $user){
                        $programme->removeUser($user);
                }
            }
            $programme->addUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            $em->flush();

            return $this->redirectToRoute('admin_programmes_home');
        }


        return $this->render('admin/programmes/edit.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerProgramme(Programmes $programme, LeconsRepository $leconsRepository, Request $request): Response
    {
        $getLecons = $leconsRepository->findBy(['programmes'=>$programme]);
        if($getLecons !== [] ){
            foreach($getLecons as $lecon){
                $programme->removeLecon($lecon);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($programme);
        $em->flush();

        $this->addFlash(
            'notice',
            'Programme supprimé avec succès'
        );
        return $this->redirectToRoute('admin_programmes_home');

    }

    /**
     * @Route("/{slug}", name="details")
     */
    public function details(ProgrammesRepository $programmesRepository, $slug): Response
    {
        $programme = $programmesRepository->findOneBy(['slug' => $slug]);
        if(!$programme instanceof \App\Entity\Programmes){
            throw new NotFoundHttpException('Le programme n\'a pas été trouvé');
        }

        return $this->render('admin/programmes/details.html.twig', ['programme' => $programme]);
    }
}
