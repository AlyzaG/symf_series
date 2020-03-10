<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Serie;
use App\Form\CategorieType;
use App\Form\SerieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppInfoController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */

    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $categorie = new Categorie();

        $categorieRepository = $this->getDoctrine()
            ->getRepository(Serie::class)
            ->findAll();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie= $form->getData();

            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('app_info/index.html.twig', [
            'categories' => $categorieRepository,
            'series' => $categorieRepository,
            'formCategorie' => $form->createView()
        ]);
    }

    /**
     * @Route("/series", name="series")
     */
    public function serie(Request $request, EntityManagerInterface $entityManager)
    {

        $serie = new Series();

        $serieRepository = $this->getDoctrine()->getRepository(Series::class)->findAll();

        $form = $this->createForm(SeriesFromType::class, $serie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $serie =$form->getData();

            $image = $serie->getAffiche();
            $imageName = md5(uniqid()).'.'.$image->guessExtension();
            $image->move($this->getParameter('upload_files'), $imageName);

            $serie->setAffiche($imageName);


            $entityManager->persist($serie);
            $entityManager->flush();

            $this->redirectToRoute('series');

        }

        return $this->render('series/index.html.twig', [
            'series' => $serieRepository,
            'formSeries' => $form->createView()
        ]);
    }

    /**
     * @Route("categorie/{id}", name="categorie")
     */
    public function categorie($id,Request $request, EntityManagerInterface $entityManager)
    {

        $categorie = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $categorie= $form->getData();

            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie');

        }

        return $this->render('app_info/categorie.html.twig', [
            'controller_name' => 'CategoriesController',
            'form'=>$form->createView(),
            'categorie'=>$categorie,
        ]);
    }

    /**
     * @Route("/series", name="series")
     */
    public function series(EntityManagerInterface $entityManager , Request $request)
    {

        $serie = new Serie();

        $serieRepository = $this->getDoctrine()
            ->getRepository(Serie::class)
            ->findAll();
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serie= $form->getData();

            $entityManager->persist($serie);
            $entityManager->flush();

            return $this->redirectToRoute('series');
        }

        return $this->render('app_info/series.html.twig', [
            'categories' => $serieRepository,
            'series' => $serieRepository,
            'formSerie' => $form->createView()
        ]);
    }



    /**
     * @Route("/series/remove/{id}", name="remove")
     */
    public function removeSerie($id, EntityManagerInterface $entityManager){
        $serie = $this->getDoctrine()->getRepository(Serie::class)->find($id);

        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("/categorie/remove/{id}", name="remove")
     */
    public function removeCategorie($id, EntityManagerInterface $entityManager){
        $serie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);

        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('categories');
    }


}
