<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('actualites_home');
        }

        return $this->render('admin/articles/ajout.html.twig', [
            'ArticleForm' => $form->createView(),
            'articles' => $article,
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