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
        $produitRepo = $entityManager->getRepository(Produit::class);
        $mouvementRepo = $entityManager->getRepository(MouvementProduit::class);

        // Statistiques de base - MODIFIÉ: utiliser userR au lieu de user
        $produitsCount = $produitRepo->count(['userR' => $user]);
        $produitsCountPrecedent = $produitRepo->countPreviousMonth($user);
        
        // Valeur du stock
        $valeurStock = $produitRepo->getValeurStock($user);
        $valeurStockPrecedent = $produitRepo->getValeurStockPreviousMonth($user);
        
        // Mouvements
        $mouvementsRecent = $mouvementRepo->findRecentByUser($user, 10);
        $mouvementsMoisCount = $mouvementRepo->countThisMonth($user);
        $mouvementsMoisPrecedent = $mouvementRepo->countPreviousMonth($user);
        
        // Statistiques 30 jours
        $stats30Jours = $mouvementRepo->getLast30DaysStats($user);
        
        return $this->render('dashboard/index.html.twig', [
            'produits_count' => $produitsCount,
            'produits_count_precedent' => $produitsCountPrecedent,
            'valeur_stock' => $valeurStock,
            'valeur_stock_precedent' => $valeurStockPrecedent,
            'mouvements_mois_count' => $mouvementsMoisCount,
            'mouvements_mois_precedent' => $mouvementsMoisPrecedent,
            'produits_faible_stock_count' => $produitRepo->countLowStock($user),
            'mouvements_recent' => $mouvementsRecent,
            'top_produits' => $produitRepo->findTopMoved($user),
            'dates_30_jours' => $stats30Jours['dates'],
            'entrees_data' => $stats30Jours['entrees'],
            'sorties_data' => $stats30Jours['sorties']
        ]);
    }
}