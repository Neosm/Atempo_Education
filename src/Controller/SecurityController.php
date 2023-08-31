<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() !== null) {

            return $this->redirectToRoute('home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last UserIdentifier entered by the user
        $lastUserIdentifier = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', [
            'last_UserIdentifier' => $lastUserIdentifier,
            'error' => $error
        ]);
    }

    /**
     * @Route("/deconnexion", name="app_logout")
     */
    public function logout(Request $request, TokenInterface $token)
    {
        $setOffline = $token->getUser()->setOnline(false);
        $this->entityManager->flush();
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
