<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\MouvementProduit; // Ajoutez cette importation
use App\Entity\User; // Ajoutez cette importation si vous utilisez la classe User
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Statistiques de base
        $produitsCount = $entityManager->getRepository(Produit::class)
            ->count(['userR' => $user]);
        
        // Deux options pour les mouvements récents :
        
        // Option 1: Utilisez la méthode du repository
        $mouvementsRecent = $entityManager->getRepository(MouvementProduit::class)
            ->findRecentByUser($user);
        
        // OU Option 2: Gardez la query directement dans le controller
        /*
        $mouvementsRecent = $entityManager->getRepository(MouvementProduit::class)
            ->createQueryBuilder('m')
            ->join('m.produit', 'p')
            ->where('p.userR = :user')
            ->setParameter('user', $user)
            ->orderBy('m.date', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
        */
        
        // Valeur totale du stock
        $valeurStock = $entityManager->getRepository(Produit::class)
            ->createQueryBuilder('p')
            ->select('SUM(p.prixUnitaire * p.stock)')
            ->where('p.userR = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        
        return $this->render('dashboard/index.html.twig', [
            'produits_count' => $produitsCount,
            'mouvements_recent' => $mouvementsRecent,
            'valeur_stock' => $valeurStock
        ]);
    }
}