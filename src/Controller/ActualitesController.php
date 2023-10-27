<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $ecole = $this->getUser()->getEcoles();
        $articles = $artsRepo->findBy(['active' =>true], ['id'=>'DESC'], ['ecoles' => $ecole]);

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
    public function details(ArticlesRepository $artsRepo, $slug, EntityManagerInterface $entityManager): Response
    {
        $article = $artsRepo->findOneBy(['slug' => $slug]);


        if(!$article instanceof \App\Entity\Articles){
            throw new NotFoundHttpException('L\'article n\'a pas été trouvé');
        }

        // Récupérer les catégories parent de l'article actuel
        $parentCategories = [];
        $category = $article->getCategories();
        while ($category !== null) {
            $parentCategories[] = $category;
            $category = $category->getParent();
        }

        // Récupérer les catégories qui ont la catégorie actuelle comme parent
        $childCategories = $entityManager->getRepository(Categories::class)
            ->findBy(['parent' => $article->getCategories()]);

        // Fusionner les catégories parent et les catégories enfants
        $allCategories = array_merge($parentCategories, $childCategories);

        // Récupérer les articles similaires basés sur les catégories parent et enfants
        $similarArticles = [];
        foreach ($allCategories as $category) {
            $similarArticles = array_merge($similarArticles, $category->getArticles()->toArray());
        }

        // Exclure l'article actuel de la liste des articles similaires
        $similarArticles = array_filter($similarArticles, function ($similarArticle) use ($article) {
            return $similarArticle !== $article;
        });

        // Limiter le nombre d'articles similaires à 3
        $similarArticles = array_slice($similarArticles, 0, 3);

        return $this->render('actualites/details.html.twig', [
            'article' => $article,
            'similarArticles' => $similarArticles,
        ]);
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
