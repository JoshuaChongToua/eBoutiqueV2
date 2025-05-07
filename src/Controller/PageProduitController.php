<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageProduitController extends AbstractController
{
    #[Route('/page-produit-index', name: 'app_page_produit')]
    public function index(): Response
    {
        return $this->render('page_produit/index.html.twig', [
            'controller_name' => 'PageProduitController',
        ]);
    }

    #[Route('/page-produit', name: 'app_get_all_produit', methods: ['GET'])]
    public function getAllProduit(ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $tendances = $produitRepository->findRandomProducts(6);
        return $this->render('page_produit/monde.html.twig', [
            'produits' => $produitRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            'tendances' => $tendances,
        ]);
    }

    #[Route('/page-produit/{continent}', name: 'app_get_continent_produit', methods: ['GET'])]
    public function getContinentProduit($continent, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->findOneBy(['nom' => $continent]);
        $produits = $produitRepository->findByType($continent);
        if ($produits) {
            return $this->render('page_produit/type.html.twig', [
                'produits' => $produits,
                'type' =>  $continent,
                'continent' => $categorie,
            ]);
        }
        return $this->render('page_produit/type.html.twig', [
            'produits' => $produitRepository->findby(['categorie' => $categorie]),
            'continent' => $categorie,
            'type' =>  $continent
        ]);
    }

}
