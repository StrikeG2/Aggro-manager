<?php

namespace App\Repository;

use App\Entity\User;
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

    public function countByUser(User $user): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.userR = :user')  // Changé de user à userR
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getValeurStock(User $user): float
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.prixUnitaire * p.stock)')
            ->where('p.userR = :user')  // Changé ici
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function countPreviousMonth(\App\Entity\User $user): int    
    {
        $date = new \DateTime('first day of last month');
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.userR = :user')  // Changé ici
            ->andWhere('p.createdAt < :date')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getValeurStockPreviousMonth(User $user): float
    {
        $date = new \DateTime('first day of this month');
        return $this->createQueryBuilder('p')
            ->select('SUM(p.prixUnitaire * p.stock)')
            ->where('p.userR = :user')  // Changé ici
            ->andWhere('p.updatedAt < :date')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function countLowStock(User $user, int $threshold = 5): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.userR = :user')  // Changé ici
            ->andWhere('p.stock < :threshold')
            ->setParameter('user', $user)
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findTopMoved(User $user, int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.nom, p.stock as quantiteStock, COUNT(m.id) as totalMouvements')
            ->join('p.mouvementProduits', 'm')  // Changé de 'p.mouvements' à 'p.mouvementProduits'
            ->where('p.userR = :user')
            ->setParameter('user', $user)
            ->groupBy('p.id')
            ->orderBy('totalMouvements', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
}
