<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/company')]

class CompanyController extends AbstractController
{
    private $em;
    private $request;

    function __construct(
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack,
    ) {
        $this->em = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Route('/check_company', name: 'api_company_check_company', methods: ['GET'])]
    public function api_company_check_company(): Response
    {
        $company = $this->em->getRepository('App\Entity\Company')->findAll();

        if (isset($company[0]))
            $response = true;
        else
            $response = false;

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/get', name: 'api_company_get', methods: ['GET'])]
    public function api_company_get(): Response
    {
        $company = $this->em->getRepository('App\Entity\Company')->findAll();
        $response = [];

        if (isset($company[0]))
            $response  = [
                'name' => $company[0]->getName(),
                'taxIdentification' => $company[0]->getTaxIdentification(),
                'address' => $company[0]->getAddress(),
                'cp' => $company[0]->getCp(),
                'city' => $company[0]->getCity(),
                'phone' => $company[0]->getPhone(),
                'email' => $company[0]->getEmail(),
            ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/edit', name: 'api_company_edit', methods: ['POST'])]
    public function api_company_edit(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        $company = $this->em->getRepository('App\Entity\Company')->findAll();

        if (isset($company[0]))
            $company[0]
                ->setname($data['payload']['name'])
                ->setTaxIdentification($data['payload']['taxIdentification'])
                ->setAddress($data['payload']['address'])
                ->setCp($data['payload']['cp'])
                ->setCity($data['payload']['city'])
                ->setPhone($data['payload']['phone'])
                ->setEmail($data['payload']['email']);
        else {
            $newCompany = new Company;
            $newCompany
                ->setname($data['payload']['name'])
                ->setTaxIdentification($data['payload']['taxIdentification'])
                ->setAddress($data['payload']['address'])
                ->setCp($data['payload']['cp'])
                ->setCity($data['payload']['city'])
                ->setPhone($data['payload']['phone'])
                ->setEmail($data['payload']['email']);

            $this->em->persist($newCompany);
        }

        $this->em->flush();
        return new JsonResponse('Company edited', Response::HTTP_OK);
    }
}
