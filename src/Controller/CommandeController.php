<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;

#[Route('/commande')]
final class CommandeController extends AbstractController
{
//    #[Route(name: 'app_commande_index', methods: ['GET'])]
//    public function index(CommandeRepository $commandeRepository): Response
//    {
//        return $this->render('commande/index.html.twig', [
//            'commandes' => $commandeRepository->findAll(),
//        ]);
//    }

/*
 * Créé une commande apres la validation de la commande
 */
    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RequestStack $requestStack, CommandeRepository $commandeRepository, ProduitRepository $produitRepository): Response
    {
        $check = $this->checkUser();
        if ($check instanceof Response) {
            return $check;
        }
        $user = $this->getUser();
        // Récupération de la session
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            return $this->redirectToRoute('app_panier');
        }

        $totalPanier = 0;
        $produits = [];

        foreach ($panier as $id => $quantity) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $produits[] = [
                    'produit' => $produit->getNom(),
                    'quantity' => $quantity
                ];
                $totalPanier += $produit->getPrix() * $quantity;
            }
        }
        do {
            $randomNumber = random_int(100000000, 999999999);
        } while ($commandeRepository->findBy(['numero' => $randomNumber]));

        $commande = new Commande();
        $commande->setNumero($randomNumber);
        $commande->setPrix($totalPanier);

        $commande->setProduit($produits);

        $commande->setAdresse($user->getAdresse());
        $commande->setUser($user);

        $entityManager->persist($commande);
        $entityManager->flush();

        $session->remove('panier');


        return $this->render('commande/merci.html.twig', [
            'user' => $user,
        ]);
    }


    /*
     * verifie que le user est bien connecté et possède une adresse
     */
    public function checkUser(): ?Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        if (!$user->getAdresse()) {
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
        }

        return null;
    }



    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(CommandeType::class, $commande);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('commande/edit.html.twig', [
//            'commande' => $commande,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
//    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($commande);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
//    }
}
