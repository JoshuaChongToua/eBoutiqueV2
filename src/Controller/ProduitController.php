<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;


#[Route('/produit')]
final class ProduitController extends AbstractController
{
    private $httpClient;
    private $session;
    private $apiKey = 'bWTszRPOGn41rxtfnu3RCdfX4Zf1WHm6CM5Rm2mlDhJVgz62pH1h5dmX';

    public function __construct(HttpClientInterface $httpClient, RequestStack $session)
    {
        $this->httpClient = $httpClient;
        $this->session = $session;
    }

//    #[Route(name: 'app_produit_index', methods: ['GET'])]
//    public function index(ProduitRepository $produitRepository): Response
//    {
//        return $this->render('produit/index.html.twig', [
//            'produits' => $produitRepository->findAll(),
//        ]);
//    }

//    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, EntityManagerInterface $entityManager)
//    {
//        $produit = new Produit();
//        $form = $this->createForm(ProduitType::class, $produit);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $response = $this->httpClient->request(
//                'GET',
//                'https://api.pexels.com/v1/search',
//                [
//                    'headers' => [
//                        'Authorization' => $this->apiKey,
//                    ],
//                    'query' => [
//                        'query' => $produit->getReference(),
//                        'per_page' => 5,
//                        'orientation' => 'landscape',
//                        'page' => random_int(1, 3),
//                    ],
//                ]
//            );
//
//            $data = $response->toArray();
//            $imageUrls = array_map(fn($photo) => $photo['src']['large'], $data['photos']);
//
//            $produit->setImage($imageUrls);
//            $produit->setNom(ucfirst($form->get('nom')->getData()));
//            $produit->setReference(ucfirst($form->get('reference')->getData()));
//            $produit->setPays(ucfirst($form->get('pays')->getData()));
//
//
//            $entityManager->persist($produit);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_produit_index');
//        }
//
//        return $this->render('produit/new.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit, Request $request): Response
    {
        // Récupérer l'URL de la page précédente
        $previousUrl = $request->headers->get('referer');

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'previousUrl' => $previousUrl,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(ProduitType::class, $produit);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('produit/edit.html.twig', [
//            'produit' => $produit,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
//    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($produit);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
//    }

    /*
     * Ajoute un produit au panier
     */
    #[Route('/{id}/add-to-cart', name: 'app_produit_add_to_cart', methods: ['GET', 'POST'])]
    public function addToCart(int $id, Request $request, RequestStack $requestStack, ProduitRepository $produitRepository): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);

        // Vérifie si le produit existe
        $produit = $produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException("Le produit n'existe pas.");
        }

        $quantiteAjoutee = $request->query->getInt('quantity', 1);

        // Mise à jour du panier
        if (isset($panier[$id])) {
            $panier[$id] += $quantiteAjoutee;
        } else {
            $panier[$id] = $quantiteAjoutee;
        }


        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }

}
