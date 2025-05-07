<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(RequestStack $requestStack, ProduitRepository $produitRepository): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);
        $panierWithData = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $panierWithData[] = [
                    'produit' => $produit,
                    'quantity' => $quantity
                ];
                $total += $produit->getPrix() * $quantity;
            }
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panierWithData,
            'total' => $total
        ]);
    }

    /*
     * Met a jour la quantité du panier et le total
     */
    #[Route('/panier/update/{id}', name: 'app_panier_update', methods: ['POST'])]
    public function updateQuantity(int $id, Request $request, RequestStack $requestStack, ProduitRepository $produitRepository): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!isset($panier[$id])) {
            throw $this->createNotFoundException('Produit non trouvé dans le panier.');
        }

        // Met à jour la quantité
        $quantity = $request->request->get('quantity');
        if ($quantity > 0) {
            $panier[$id] = $quantity;
        }

        // Met à jour le panier dans la session
        $session->set('panier', $panier);

        $produit = $produitRepository->find($id);
        $nouveauTotalProduit = $produit->getPrix() * $quantity;

        // Recalculer le total du panier
        $totalPanier = 0;
        foreach ($panier as $productId => $quantite) {
            $product = $produitRepository->find($productId);
            $totalPanier += $product->getPrix() * $quantite;
        }

        $response = new Response($this->renderView('panier/_ligne_produit.html.twig', [
            'produit' => $produit,
            'quantity' => $quantity,
            'total' => $nouveauTotalProduit,
            'totalPanier' => $totalPanier,
        ]));

        $response->headers->set('Content-Type', 'text/vnd.turbo-stream.html');

        return $response;
    }

    /*
     * supprime un élément du panier et met à jour le total
     */
    #[Route('/panier/remove/{id}', name: 'app_panier_remove', methods: ['POST'])]
    public function removeProduct(int $id, RequestStack $requestStack, ProduitRepository $produitRepository): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!isset($panier[$id])) {
            throw $this->createNotFoundException('Produit non trouvé dans le panier.');
        }

        // Retirer le produit du panier
        unset($panier[$id]);
        $session->set('panier', $panier);

        // Recalculer le total du panier
        $totalPanier = array_reduce(array_keys($panier), function ($total, $productId) use ($produitRepository, $panier) {
            $product = $produitRepository->find($productId);
            return $total + ($product ? $product->getPrix() * $panier[$productId] : 0);
        }, 0);

        // Retourne un Turbo Stream pour supprimer la ligne et mettre à jour le total
        return $this->render('panier/_panier_total.html.twig', [
            'id' => $id,
            'totalPanier' => $totalPanier,
            'panier' => $panier,
        ], new Response('', Response::HTTP_OK, ['Content-Type' => 'text/vnd.turbo-stream.html']));
    }



}
