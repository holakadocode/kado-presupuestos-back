<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/provider')]

class ProviderController extends AbstractController
{
    #[Route('/list', name:'apiProviderList', methods: ['GET'])]
    public function apiProviderList(): Response
    {
        return $this->render('provider/index.html.twig', [
            'controller_name' => 'ProviderController',
        ]);
    }
    #[Route('/add', name:'apiProviderAdd', methods: ['PUT'])]
    public function apiProviderAdd(): Response
    {
        return $this->render('provider/index.html.twig', [
            'controller_name' => 'ProviderController',
        ]);
    }
}
