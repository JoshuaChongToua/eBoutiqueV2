<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategorieCrudController extends AbstractCrudController
{
    private $httpClient;
    private $apikeyUnsplash= "TXWJMwr83pP7CgyTZ7DaHC7smEdLx9TPq5NrEgh9aVw";
    public function __construct(HttpClientInterface $httpClient, RequestStack $session)
    {
        $this->httpClient = $httpClient;
    }
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Categorie) return;

        // Requête à l'API d'Unsplash
        $response = $this->httpClient->request('GET', 'https://api.unsplash.com/search/photos', [
            'query' => [
                'query' => $entityInstance->getNom(),
                'per_page' => 10, // Nombre d'images par page
                'orientation' => 'landscape', // Orientation de l'image
                'page' => 1, // Page aléatoire entre 1 et 3
                'client_id' => $this->apikeyUnsplash, // Remplacez par votre clé API,
                'lang' => 'fr'
            ],
        ]);

        $data = $response->toArray();

        // Vérifier le nombre d'images trouvées
        if (isset($data['total']) && $data['total'] > 0) {
            // Récupérer les URLs des images en haute qualité
            $imageUrls = array_map(fn($photo) => $photo['urls']['regular'], $data['results']);
        } else {
            $imageUrls = []; // Pas d'images trouvées
        }

        // Assigner les images à l'instance
        $entityInstance->setImage($imageUrls);
        $entityInstance->setNom(ucfirst($entityInstance->getNom()));

        parent::persistEntity($entityManager, $entityInstance);
    }
}
