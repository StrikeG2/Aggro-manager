<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Redirige vers le dashboard si connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }
        
        return $this->render('home/index.html.twig');
    }
}
