<?php

namespace App\Controller;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;

class HeaderSearchController extends AbstractController
{
    /**
     * @Route("/header/search", name="header_search")
     */
    public function headerSearch(): jsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->findAll();
        $articles = $em->getRepository(Articles::class)->findAll();
        $getdatas = [];
        foreach($user as $value){
            $getdatas[]["user"] = $value->getName().' '.$value->getEmail();
        }
        foreach($articles as $value){
            $getdatas[]["article"] = $value->getTitle();
        }

        $datas = array();
        foreach($getdatas as $key => $values){
                $datas[$key] = $values;
        }

        return new jsonResponse($datas);
    }

}
