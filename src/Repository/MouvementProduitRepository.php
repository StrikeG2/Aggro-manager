<?php

namespace App\Repository;

use App\Entity\MouvementProduit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MouvementProduit>
 */
class MouvementProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementProduit::class);
    }
    
    public function findRecentByUser(User $user, int $limit = 5): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.produit', 'p')
            ->where('p.userR = :user')
            ->setParameter('user', $user)
            ->orderBy('m.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return MouvementProduit[] Returns an array of MouvementProduit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MouvementProduit
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
