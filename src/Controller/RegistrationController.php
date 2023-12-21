<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    // private $emailVerifier;

    // public function __construct(EmailVerifier $emailVerifier)
    // {
    //     $this->emailVerifier = $emailVerifier;
    // }

    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
    {
        $notification = null;
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        $ecole =  $this->getUser()->getEcoles();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $search_email = $entityManager->getRepository(Users::class)->findOneByEmail($user->getEmail());
            $user = $form->getData();
            $user->setEcoles($ecole);
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
            }else{
                $user->setThumbnail('default-profile-pic.jpg');
            }
            if (!$search_email) {
                $password = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
            } else {
                $notification = "Email non valide ou déjà inscrite sur le site, essayez une nouvelle adresse mail ou connectez vous à votre compte";
            }            
            $user->setIdUnique(uniqid(mt_rand(), true));
            $user->setOnline(false);
            $user->setIsVerified(false);
            $entityManager->persist($user);
            $entityManager->flush();
            $notification = "Merci de votre inscription, vous pouvez vous connecter à votre compte";
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('registration/register.html.twig', [
            'RegisterForm' => $form->createView(),
            'notification' =>$notification
        ]);
    }

    // /**
    //  * @Route("/verify/email", name="app_verify_email")
    //  */
    // public function verifyUserEmail(Request $request): Response
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     // validate email confirmation link, sets User::isVerified=true and persists
    //     try {
    //         $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
    //     } catch (VerifyEmailExceptionInterface $exception) {
    //         $this->addFlash('verify_email_error', $exception->getReason());

    //         return $this->redirectToRoute('app_register');
    //     }
    //     $this->addFlash('success', 'Votre adresse électronique a été vérifiée.');

    //     return $this->redirectToRoute('home');
    // }
}
