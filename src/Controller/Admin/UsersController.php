<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\EditProfileType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route("/admin/users", name="admin_users_")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(UsersRepository $usersRepository): Response
    {
        $getAll = $usersRepository->findAll();

        return $this->render('/admin/users/index.html.twig', [
            'users'=>$getAll,
        ]);
    }

    /**
     * @Route("/eleves", name="student")
     */
    public function eleves(UsersRepository $usersRepository): Response
    {
        $userData = $usersRepository->findByrolesStudent();

        return $this->render('admin/users/index.html.twig', [
            'users' => $userData,
        ]);
    }

    /**
     * @Route("/professeurs", name="teacher")
     */
    public function professeurs(UsersRepository $usersRepository): Response
    {
        $userData = $usersRepository->findByrolesTeacher();

        return $this->render('admin/users/index.html.twig', [
            'users' => $userData,
        ]);
    }

    /**
     * @Route("/superprofesseurs", name="superteacher")
     */
    public function Superprofesseurs(UsersRepository $usersRepository): Response
    {
        $userData = $usersRepository->findByrolesSuperTeacher();

        return $this->render('admin/users/index.html.twig', [
            'users' => $userData,
        ]);
    }

    /**
     * @Route("/administrateurs", name="admin")
     */
    public function admin(UsersRepository $usersRepository): Response
    {
        $userData = $usersRepository->findByrolesAdmin();

        return $this->render('admin/users/index.html.twig', [
            'users' => $userData,
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(Users $users): Response
    {

        return $this->render('admin/users/index.html.twig', [
            'user'=>$users
        ]);
    }


    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerUser(Request $request, Users $users): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($users);
        $em->flush();

        $this->addFlash('message', 'Utilisateur supprimé avec succès');
        return $this->redirectToRoute('admin_users_home');
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierUser(Request $request, Users $users, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EditProfileType::class, $users);
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
                    $users->setThumbnail($newFilename);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($users);
                $em->flush();
                $this->addFlash('message_sucess', "Les informations de l'utilisateur ont pu être mis à jour.");
            }else {
                $this->addFlash('message_alert',"Les informations de l'utilisateur n'ont pas pu être mis à jour");
            }
            return $this->redirectToRoute('admin_users_home');
        }

        return $this->render('admin/users/informations.html.twig',[
            'form'=> $form->CreateView(),
            'user'=>$users
        ]);
    }

}
