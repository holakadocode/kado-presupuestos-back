<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/company')]

class CompanyController extends AbstractController
{
    private $em;
    private $request;
    private $validator;

    function __construct(
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack,
        ValidatorInterface $validatorInterface
    ) {
        $this->em = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validatorInterface;
    }

    #[Route('/list', name: 'api_company_list', methods: ['GET'])]
    public function api_company_list(): Response
    {
        $company = $this->em->getRepository('App\Entity\Company')->findAll();

        $response = [];

        foreach ($company as $company) {
            $response[] = [
                'id' => $company->getId(),
                'name' => $company->getName(),
                'taxIdentification' => $company->getTaxIdentification(),
                'address' => $company->getAddress(),
                'cp' => $company->getCp(),
                'city' => $company->getCity(),
                'phone' => $company->getPhone(),
                'email' => $company->getEmail(),

            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/add', name: 'api_company_Add', methods: ['PUT'])]
    public function api_company_add(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        $newCompany = new Company();
        $newCompany
            ->setName($data['name'])
            ->setTaxIdentification($data['taxIdentification'])
            ->setAddress($data['address'])
            ->setCp($data['cp'])
            ->setCity($data['city'])
            ->setPhone($data['phone'])
            ->setEmail($data['email']);
           


        $errors = $this->validator->validate($newCompany);

        if (count($errors) > 0) {
            return new JsonResponse('Validacion error', Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->em->persist($newCompany);
        $this->em->flush();

        return new JsonResponse('Company add', Response::HTTP_OK);
    }

    #[Route('/edit', name: 'api_company_edit', methods: ['POST'])]
    public function api_company_edit(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$companyToEdit = $this->em->getRepository('App\Entity\Company')->findOneById($data['companyID']))
            return new JsonResponse('Company not found', Response::HTTP_NOT_FOUND);


        $companyToEdit
            ->setname($data['payload']['name'])
            ->setTaxIdentification($data['payload']['taxIdentification'])
            ->setAddress($data['payload']['address'])
            ->setCp($data['payload']['cp'])
            ->setCity($data['payload']['city'])
            ->setPhone($data['payload']['phone'])
            ->setEmail($data['payload']['email']);
            
            

        $this->em->flush();
        return new JsonResponse('Company edited', Response::HTTP_OK);
    }

    #[Route('/delete', name: 'api_company_delete', methods: ['DELETE'])]
    public function api_company_delete(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$data || !isset($data['companyID'])) {
            return new JsonResponse('Invalid company data', Response::HTTP_BAD_REQUEST);
        }

        $company = $this->em->getRepository('App\Entity\Company')->findOneById($data['companyID']);

        if (!$company) {
            return new JsonResponse('Company not found', Response::HTTP_NOT_FOUND);
        }

       
        $this->em->remove($company);
        $this->em->flush();

        return new JsonResponse('Company deleted', Response::HTTP_OK);
    }
}
