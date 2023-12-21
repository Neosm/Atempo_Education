<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route ("/admin/articles", name="admin_articles_")
 * @package App\Controller
 */


class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticlesRepository $artsRepo): Response
    {

        return $this->render('admin/articles/index.html.twig', [
            'articles' => $artsRepo->findBy([], ['id' => 'DESC']),
        ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutArticle(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Articles;
        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(ArticlesType::class, $article, ['ecole' => $ecole]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUsers($this->getUser());
            $article->setActive(true);
            $article->setSlug($slugger->slug($form->get('title')->getData()));
            $article->setEcoles($ecole);

            $images = $form->get('illustrations')->getData();

            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $images->guessExtension();

                try {
                    $images->move(
                        $this->getParameter('illustration_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $article->setIllustrations($newFilename);
            }else{
                $article->setIllustrations('default-thumbnails.jpg');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('admin_articles_home');
        }

        return $this->render('admin/articles/ajout.html.twig', [
            'ArticleForm' => $form->createView(),
            'articles' => $article,
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

        return $this->render('admin/articles/details.html.twig', [
            'article' => $article,
            'similarArticles' => $similarArticles,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierArticle(Request $request, articles $article, SluggerInterface $slugger): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(ArticlesType::class, $article, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('illustrations')->getData();
            $article->setSlug($slugger->slug($form->get('title')->getData()));

            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $images->guessExtension();

                try {
                    $images->move(
                        $this->getParameter('illustration_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $article->setIllustrations($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('admin_articles_home');
        }
        return $this->render('admin/articles/ajout.html.twig', [
            'ArticleForm' => $form->createView(),
            'articles' => $article,
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerArticle(Request $request, articles $article): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash('message', 'Article supprimé avec succès');
        return $this->redirectToRoute('admin_articles_home');

    }

    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(articles $article): Response
    {
        $article->setActive(!(bool) $article->isActive());

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return new Response("true");
    }
}