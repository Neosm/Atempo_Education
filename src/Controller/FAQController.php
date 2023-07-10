<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FAQController extends AbstractController
{
    /**
     * @Route("/foire-aux-questions", name="faq")
     */
    public function index(): Response
    {
        return $this->render('faq/index.html.twig');
    }
}
