<?php

namespace App\Controller;

use App\Entity\Budget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/budget')]
class BudgetController extends AbstractController
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

    #[Route('/list', name: 'api_budget_list', methods: ['GET'])]
    public function api_budget_list(): JsonResponse
    {
        $budgets = $this->em->getRepository('App\Entity\Budget')->findAll();

        $response = [];
        foreach ($budgets as $budget) {
            $response[] = [
                'id' => $budget->getId(),
                'dateTime' => $budget->getDateTime()->format('d/m/Y'),
                'title' => $budget->getTitle(),
                'iva' => $budget->getIva(),
                'total' => $budget->getTotal(),
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/get-data', name: 'api_budget_getData', methods: ['GET'])]
    public function api_budget_getData(): JsonResponse
    {
        $company = $this->em->getRepository('App\Entity\Company')->findOneById(1);
        $clients = $this->em->getRepository('App\Entity\Client')->findAll();

        $response['ownCompany'] = [
            'name' => $company->getName(),
            'taxIdentification' => $company->getTaxIdentification(),
            'address' => $company->getAddress(),
            'cp' => $company->getCp(),
            'city' => $company->getCity(),
            'phone' => $company->getPhone(),
            'email' => $company->getEmail(),
        ];

        foreach ($clients as $client) {
            $response['clients'][] = [
                'label' => $client->getName() . ' - ' . $client->getSurname(),
                'value' => $client->getId(),
                'address' => $client->getAddress(),
                'cp' => $client->getCp(),
                'taxIdentification' => $client->getTaxIdentification(),
                'city' => $client->getCity(),
                'phone' => $client->getTlf(),
                'email' => $client->getContactEmail(),
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
    #[Route('/add', name: 'api_budget_add', methods: ['PUT'])]
    public function api_budget_add(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $newBudget = new Budget;

        $newBudget
            ->setDateTime(new \DateTime())
            ->setTitle($data['payload']['title'])
            ->setIva($data['payload']['iva'])
            ->setTotal($data['payload']['total']);

        $this->em->persist($newBudget);
        $this->em->flush();


        return new JsonResponse('', Response::HTTP_OK);
    }

    #[Route('/edit', name: 'api_budget_edit', methods: ['POST'])]
    public function api_budget_edit(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $budget = $this->em->getRepository('App\Entity\Budget')->findOneBy($data['budgetID']);
        if (!$budget)
            return new JsonResponse('Budget not found', Response::HTTP_NOT_FOUND);

        $budget
            ->setDateTime(new \DateTime())
            ->setTitle($data['payload']['title'])
            ->setIva($data['payload']['iva'])
            ->setTotal($data['payload']['total']);

        $this->em->flush();

        return new JsonResponse('', Response::HTTP_OK);
    }

    #[Route('/delete', name: 'api_budget_delete', methods: ['DELETE'])]
    public function api_budget_delete(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $budget = $this->em->getRepository('App\Entity\Budget')->findOneBy($data['budgetID']);

        $this->em->remove($budget);
        $this->em->flush();

        return new JsonResponse('holi chuli', Response::HTTP_OK);
    }
}
