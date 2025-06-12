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

    public function countThisMonth(User $user): int
    {
        $startDate = new \DateTime('first day of this month');
        $endDate = new \DateTime('last day of this month');

        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->join('m.produit', 'p')
            ->where('p.userR = :user')
            ->andWhere('m.date BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPreviousMonth(User $user): int
    {
        $startDate = new \DateTime('first day of last month');
        $endDate = new \DateTime('last day of last month');

        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->join('m.produit', 'p')
            ->where('p.userR = :user')
            ->andWhere('m.date BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLast30DaysStats(User $user): array
    {
        $startDate = new \DateTime('-29 days');
        $endDate = new \DateTime();

        // Solution alternative qui fonctionne avec PostgreSQL
        $results = $this->createQueryBuilder('m')
            ->select([
                "m.date as full_date",
                'm.type', 
                'COUNT(m.id) as count'
            ])
            ->join('m.produit', 'p')
            ->where('p.userR = :user')
            ->andWhere('m.date BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy("m.date, m.type") // Group by direct sur le champ
            ->getQuery()
            ->getResult();

        // Traitement des dates en PHP
        $groupedResults = [];
        foreach ($results as $result) {
            $date = $result['full_date']->format('Y-m-d');
            $type = $result['type'];
            
            if (!isset($groupedResults[$date][$type])) {
                $groupedResults[$date][$type] = 0;
            }
            $groupedResults[$date][$type] += $result['count'];
        }

        return $this->formatStatsData($groupedResults);
    }

    private function formatStatsData(array $groupedResults): array
    {
        $formatted = ['entrees' => [], 'sorties' => []];
        $dates = [];
        
        // Générer toutes les dates des 30 derniers jours
        for ($i = 29; $i >= 0; $i--) {
            $date = (new \DateTime("-$i days"))->format('Y-m-d');
            $dates[] = $date;
            $formatted['entrees'][$date] = $groupedResults[$date]['entree'] ?? 0;
            $formatted['sorties'][$date] = $groupedResults[$date]['sortie'] ?? 0;
        }

        return [
            'dates' => $dates,
            'entrees' => array_values($formatted['entrees']),
            'sorties' => array_values($formatted['sorties'])
        ];
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
