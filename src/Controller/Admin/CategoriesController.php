<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route ("/admin/categories", name="admin_categories_")
 * @package App\Controller
 */


class  CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoriesRepository $catsRepo): Response
    {

        return $this->render('admin/categories/index.html.twig', [
            'categories' => $catsRepo->findBy([],['id'=>'DESC']),
        ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutCategorie(Request $request, SluggerInterface $slugger): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $categorie = new Categories;
        $form = $this->createForm(CategoriesType::class, $categorie, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setEcoles($ecole);
            $categorie->setSlug($slugger->slug($form->get('name')->getData()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }
        return $this->render('admin/categories/ajout.html.twig', [
            'categorie_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierCategorie(Request $request, categories $categorie): Response
    {
        $ecole = $this->getUser()->getEcoles();
        $form = $this->createForm(CategoriesType::class, $categorie, ['ecole' => $ecole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }
        return $this->render('admin/categories/ajout.html.twig', [
            'categorie_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerCategories(Request $request, categories $categorie): Response
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();

        $this->addFlash('message', 'Catégorie supprimé avec succès');
        return $this->redirectToRoute('admin_categories_home');

    }

}