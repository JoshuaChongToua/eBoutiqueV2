<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProduitCrudController extends AbstractCrudController
{
    private $httpClient;
    private $apikeyUnsplash= "TXWJMwr83pP7CgyTZ7DaHC7smEdLx9TPq5NrEgh9aVw";
    private $apiKey = 'bWTszRPOGn41rxtfnu3RCdfX4Zf1WHm6CM5Rm2mlDhJVgz62pH1h5dmX';

    public function __construct(HttpClientInterface $httpClient, RequestStack $session)
    {
        $this->httpClient = $httpClient;
    }
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Informations générales'),
            TextField::new('nom', 'Nom du produit'),
            TextField::new('reference', 'Référence'),
            AssociationField::new('categorie', 'Catégorie')
                ->setCrudController(CategorieCrudController::class) // Lien vers le CRUD des catégories
                ->setRequired(true) // Facultatif, si tu veux que le champ soit obligatoire
                ->setFormTypeOptions([
                    'choice_label' => 'nom', // Affiche le nom de la catégorie dans la liste déroulante
                ])
                ->formatValue(fn($value, $entity) => $entity->getCategorie() ? $entity->getCategorie()->getNom() : 'Aucune catégorie'),
            TextField::new('prix', 'Prix'),
            TextField::new('description', 'Description'),
            TextField::new('pays', 'Pays'),
            ChoiceField::new('type', 'Type')
                ->setChoices([
                    'Ville' => 'ville',
                    'Pays' => 'pays',
                ])
                ->renderExpanded()
                ->setRequired(true),
            ChoiceField::new('environnement', 'Environnement')
                ->setChoices([
                    'Ville' => 'ville',
                    'Plage' => 'plage',
                    'Montagne' => 'montagne',
                    'Nature' => 'nature',
                ])
                ->allowMultipleChoices()
                ->renderExpanded()
                ->setRequired(true),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Produit) return;

        // Requête à l'API d'Unsplash
        $response = $this->httpClient->request('GET', 'https://api.unsplash.com/search/photos', [
            'query' => [
                'query' => $entityInstance->getReference(),
                'per_page' => 10, // Nombre d'images par page
                'orientation' => 'landscape', // Orientation de l'image
                'page' => 1, // Page aléatoire entre 1 et 3
                'client_id' => $this->apikeyUnsplash, // Remplacez par votre clé API
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
        $entityInstance->setReference(ucfirst($entityInstance->getReference()));
        $entityInstance->setPays(ucfirst($entityInstance->getPays()));

        parent::persistEntity($entityManager, $entityInstance);
    }




}
