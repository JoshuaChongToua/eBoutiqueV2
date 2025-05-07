<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NavbarController extends AbstractController
{
    #[Route('/navbar', name: 'app_navbar')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $continent = $categorieRepository->findAll();
        $user = $this->getUser();
        $roles = [];
        if ($user) {
            $roles = $user->getRoles();
        }
        return $this->render('navbar/index.html.twig', [
            'controller_name' => 'NavbarController',
            'roles' => $roles,
            'user' => $user,
            'continents' => $continent,
        ]);
    }
}
