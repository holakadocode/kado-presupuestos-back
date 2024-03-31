<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class DefaultController extends AbstractController
{
    #[Route('/login', name: 'admin_login')]
    public function adminLogin(): Response
    {
        return $this->render('Admin/AdminLogin.html.twig', [
            'test' => 'x',
        ]);
    }



    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test()
    {
        return new JsonResponse('holi chuli', Response::HTTP_OK);
    }
}
