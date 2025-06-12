<?php

namespace App\Controller;

use App\Entity\MouvementProduit;
use App\Form\MouvementProduitForm;
use App\Repository\MouvementProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mouvement/produit')]
final class MouvementProduitController extends AbstractController
{
    #[Route(name: 'app_mouvement_produit_index', methods: ['GET'])]
    public function index(MouvementProduitRepository $mouvementProduitRepository): Response
    {
        return $this->render('mouvement_produit/index.html.twig', [
            'mouvement_produits' => $mouvementProduitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mouvement_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mouvementProduit = new MouvementProduit();
        $mouvementProduit->setDate(new \DateTimeImmutable());
        
        $form = $this->createForm(MouvementProduitForm::class, $mouvementProduit, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le prix unitaire du produit associé
            $produit = $mouvementProduit->getProduit();
            $mouvementProduit->setPrixUnitaire($produit->getPrixUnitaire());
            
            // Mise à jour du stock
            if ($mouvementProduit->getType() === 'entree') {
                $produit->setStock($produit->getStock() + $mouvementProduit->getQuantite());
            } else {
                $produit->setStock($produit->getStock() - $mouvementProduit->getQuantite());
            }
            
            $entityManager->persist($mouvementProduit);
            $entityManager->flush();

            return $this->redirectToRoute('app_mouvement_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mouvement_produit/new.html.twig', [
            'mouvement_produit' => $mouvementProduit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_mouvement_produit_show', methods: ['GET'])]
    public function show(MouvementProduit $mouvementProduit): Response
    {
        return $this->render('mouvement_produit/show.html.twig', [
            'mouvement_produit' => $mouvementProduit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mouvement_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MouvementProduit $mouvementProduit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MouvementProduitForm::class, $mouvementProduit, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pour l'édition, conservez le prix unitaire existant
            $entityManager->flush();

            return $this->redirectToRoute('app_mouvement_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mouvement_produit/edit.html.twig', [
            'mouvement_produit' => $mouvementProduit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_mouvement_produit_delete', methods: ['POST'])]
    public function delete(Request $request, MouvementProduit $mouvementProduit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mouvementProduit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mouvementProduit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mouvement_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
