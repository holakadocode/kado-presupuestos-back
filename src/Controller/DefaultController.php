<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\test\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]

class DefaultController extends AbstractController
{
    private $request;
    private $authenticationUtils;
    private $em;

    
    private $validator;

    function __construct(
        RequestStack $requestStack,
        AuthenticationUtils $authenticationUtils,
        EntityManagerInterface $entityManagerInterface,
        
        ValidatorInterface $validatorInterface
    ) {
        $this->em = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validatorInterface;
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test()
    {
        return new HttpFoundationJsonResponse('holi chuli', Response::HTTP_OK);
    }
}
