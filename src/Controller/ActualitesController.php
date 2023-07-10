<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actualites", name="actualites_")
 * @package App\Controller
 */

class ActualitesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticlesRepository $artsRepo, CategoriesRepository $catsRepo, Request $request): Response
    {
        $articles = $artsRepo->findBy(['active' =>true], ['id'=>'DESC']);

        // On définit le nombre d'éléments par page
        $limit = 8;

        // On récupère le numéro de page
        $page = (int)$request->query->get("page", 1);

        // On récupère les filtres
        $filters = $request->get("categories");

        // On récupère les annonces de la page en fonction du filtre
        $articles = $artsRepo->getPaginatedArticles($page, $limit, $filters);

        // On récupère le nombre total d'articles
        $totalarticles = $artsRepo->getTotalArticles($filters);


        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView('actualites/_content.html.twig', ['articles' => $articles, 'totalarticles' => $totalarticles, 'limit' => $limit, 'page' => $page])
            ]);
        }
        // On va chercher toutes les catégories
        $categories = $catsRepo->findAll();

        return $this->render('actualites/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'totalarticles' =>$totalarticles,
            'limit'=>$limit,
            'page' =>$page,
        ]);
    }

    /**
     * @Route("/article/{slug}", name="details")
     */
    public function details(ArticlesRepository $artsRepo, $slug): Response
    {
        $article = $artsRepo->findOneBy(['slug' => $slug]);


        if(!$article instanceof \App\Entity\Articles){
            throw new NotFoundHttpException('L\'article n\'a pas été trouvé');
        }

        return $this->render('actualites/details.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/favoris/ajout/{id}", name="ajout_favoris")
     */
    public function ajoutFavoris(Articles $articles): \Symfony\Component\HttpFoundation\Response
    {
        if(!$articles){
            throw new NotFoundHttpException('Pas d\'article trouvée');
        }
        $articles->addFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($articles);
        $em->flush();
        return $this->redirectToRoute('actualites_home');
    }

    /**
     * @Route("/favoris/retrait/{id}", name="retrait_favoris")
     */
    public function retraitFavoris(Articles $articles): \Symfony\Component\HttpFoundation\Response
    {
        if(!$articles){
            throw new NotFoundHttpException('Pas d\'article trouvée');
        }
        $articles->removeFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($articles);
        $em->flush();
        return $this->redirectToRoute('actualites_home');
    }
}
