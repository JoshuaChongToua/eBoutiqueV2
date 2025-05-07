<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByType(string $type)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.environnement LIKE :type')
            ->setParameter('type', '%"'.$type.'"%')
            ->getQuery()
            ->getResult();
    }

    public function findRandomProducts(int $limit = 6)
    {
        // Récupérer tous les IDs des produits
        $ids = $this->createQueryBuilder('p')
            ->select('p.id')
            ->getQuery()
            ->getScalarResult();

        // Extraire les IDs sous forme de tableau
        $ids = array_column($ids, 'id');

        // Mélanger les IDs et en prendre 6
        shuffle($ids);
        $randomIds = array_slice($ids, 0, $limit);

        // Récupérer les produits correspondants
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $randomIds)
            ->getQuery()
            ->getResult();
    }





}
