<?php

namespace App\Controller;

use App\Entity\Lecons;
use App\Form\LeconsType;
use App\Repository\LeconsRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route("/lecons", name="lecons_")
 */
class LeconsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(LeconsRepository $leconsRepository): Response
    {
        $allLecons = $leconsRepository->findAll();

        return $this->render('lecons/index.html.twig', [
            'lecons'=> $allLecons
        ]);
    }

    /**
     * @Route("/ajouter", name="add")
     */
    public function ajouter(Request $request, SluggerInterface $slugger): Response
    {
        $lecon = new Lecons();
        $form = $this->createForm(LeconsType::class, $lecon, ['required' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lecon->addUser($this->getUser());
            $images = $form->get('thumbnails')->getData();
            $lecon->setSlug($slugger->slug($form->get('nom')->getData()));
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
                $lecon->setThumbnails($newFilename);
            }
            $pdf = $form->get('pdf')->getData();
            if ($pdf) {
                $originalFilename = pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdf->guessExtension();

                try {
                    $pdf->move(
                        $this->getParameter('lecons_pdfs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $lecon->setPdf($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($lecon);
            $em->flush();

            return $this->redirectToRoute('lecons_home');
        }


        return $this->render('lecons/add.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/publiques", name="all_public")
     */
    public function public(LeconsRepository $leconsRepository): Response
    {
        $allLecons = $leconsRepository->findAll();

        return $this->render('lecons/all.html.twig', [
            'lecons'=> $allLecons
        ]);
    }

    /**
     * @Route("/prives", name="all_privee")
     */
    public function privees(LeconsRepository $leconsRepository): Response
    {
        $allLecons = $leconsRepository->findAll();

        return $this->render('lecons/all.html.twig', [
            'lecons'=> $allLecons
        ]);
    }

    /**
     * @Route("/{slug}", name="details")
     */
    public function details(LeconsRepository $leconsRepository, $slug): Response
    {
        $lecon = $leconsRepository->findOneBy(['slug' => $slug]);
        if(!$lecon instanceof \App\Entity\Lecons){
            throw new NotFoundHttpException('La leçon n\'a pas été trouvé');
        }

        return $this->render('lecons/details.html.twig', ['lecon' => $lecon]);
    }

    /**
     * @Route("/{slug}/pdf/download", name="pdf_download")
     */
    public function downloadPdf(LeconsRepository $leconsRepository, $slug): Response
    {
        $lecon = $leconsRepository->findOneBy(['slug' => $slug]);
        $getPdf = $lecon->getPdf();
        $response = new BinaryFileResponse('uploads/lecons/pdf/'.$getPdf);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$getPdf);
        return $response;
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierLecon(Request $request, Lecons $lecons, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(LeconsType::class, $lecons, ['required' => false]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $lecons->setSlug($slugger->slug($form->get('nom')->getData()));

            $images = $form->get('thumbnails')->getData();

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
                $lecons->setThumbnails($newFilename);
            }
            $pdf = $form->get('pdf')->getData();
            if ($pdf) {
                $originalFilename = pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdf->guessExtension();

                try {
                    $pdf->move(
                        $this->getParameter('lecons_pdfs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $lecons->setPdf($newFilename);
            }

            if($lecons->getProgrammesLecons()->getId() == 1) {
                $getUsers = $form->get('users')->getData();
                foreach($getUsers as $user){
                    $lecons->removeUser($user);
                }
            }
            $lecons->addUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($lecons);
            $em->flush();

            return $this->redirectToRoute('lecons_home');
        }
        return $this->render('lecons/add.html.twig', [
            'form' => $form->createView(),
            'lecon' => $lecons,
        ]);
    }

     /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerProgramme(Lecons $lecon): Response
    {
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($lecon);
        $em->flush();

        return $this->redirectToRoute('lecons_home');

    }
}
