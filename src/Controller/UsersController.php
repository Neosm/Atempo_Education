<?php

namespace App\Controller;

use App\Form\DefinedNewPasswordType;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UsersController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/utilisateur", name="users")
     */
    public function utilisateur(): Response
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/utilisateur/mot-de-passe", name="users_password")
     */
    public function NewPassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification_miss = null;
        $notification_good = null;
        $user = $this->getUser();
        $form = $this->createForm(DefinedNewPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $old_pwd = $form->get('old_password')->getData();
            if ($passwordHasher->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $password = $passwordHasher->hashPassword($user, $new_pwd);

                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $notification_good = "Votre mot de passe à bien était mis à jour.";
            }else{
                $notification_miss = "Votre mot de passe actuel n'est pas valide";
            }
        }

        return $this->render('users/password.html.twig',[
            'form'=> $form->CreateView(),
            'notification_good'=>$notification_good,
            'notification_miss'=>$notification_miss
        ]);
    }

    /**
     * @Route("/utilisateur/informations", name="users_informations")
     */
    public function NewInformations(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if ($form->isValid()) {
                $thumbnail = $form->get('thumbnail')->getData();
                if ($thumbnail) {
                    $originalFilename = pathinfo($thumbnail->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $thumbnail->guessExtension();

                    try {
                        $thumbnail->move(
                            $this->getParameter('thumbnails_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $user->setThumbnail($newFilename);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->addFlash('message_sucess', "Vos informations ont pu être mis à jour.");
            }else {
                $this->addFlash('message_alert',"Vos informations n'ont pas pu être mis à jour");
            }
            return $this->redirectToRoute('users');
        }

        return $this->render('users/information.html.twig',[
            'form'=> $form->CreateView(),
        ]);
    }

    /**
     * @Route("/utilisateur/donnees", name="users_data")
     */
    public function users_data(): Response
    {
    //TODO Il faut faire la récolte de donnée et la mise en page de celle-ci
        return $this->render('users/données.html.twig');
    }


    /**
     * @Route("/utilisateur/donnees/download", name="users_data_download")
     */
    public function users_data_download(): Response
    {
        // On définit les options du PDF
        //TODO Il faut faire la mise en page du pdf via le twig
        $pdfOptions = new Options();
        // Police par défaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // On instancie Dompdf
        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);

        // On génère le html
        $html = $this->renderView('users/download.html.twig');

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier
        $fichier = 'user-data-'.$this->getUser()->getName().'-'.$this->getUser()->getId() .'.pdf';

        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        return new Response();
    }


}
