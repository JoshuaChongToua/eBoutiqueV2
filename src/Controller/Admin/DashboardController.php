<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')] // Ajout de la route manuellement
    public function index(): Response
    {
//        return parent::index();
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(CategorieCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EBoutique');
    }

    public function configureMenuItems(): iterable
    {
         yield MenuItem::linkToCrud('Categorie', 'fas fa-list', Categorie::class);
         yield MenuItem::linkToCrud('Commande', 'fas fa-list', Commande::class);
         yield MenuItem::linkToCrud('Produit', 'fas fa-list', Produit::class);
         yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
    }
}
