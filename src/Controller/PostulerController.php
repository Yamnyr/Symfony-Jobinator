<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostulerController extends AbstractController
{
    #[Route('/postuler', name: 'app_postuler')]
    public function index(): Response
    {
        return $this->render('postuler/index.html.twig', [
            'controller_name' => 'PostulerController',
        ]);
    }
}
