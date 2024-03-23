<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Budget;
use App\Entity\BudgetArticle;
use DateTime;
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
                'clientID' => $budget->getClient()->getId(),
                'clientName' => $budget->getClientName(),
                'iva' => $budget->getIva(),
                'total' => $budget->getTotal(),
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/get-data', name: 'api_budget_getData', methods: ['POST'])]
    public function api_budget_getData(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $company = $this->em->getRepository('App\Entity\Company')->findOneById(1);
        $clients = $this->em->getRepository('App\Entity\Client')->findAll();
        $folders = $this->em->getRepository('App\Entity\FamilyFolder')->findAll();

        $response['articles'] = [];
        foreach ($folders as $folder) {
            $response['articles'][] = [
                'label' => $folder->getName(),
                'value' => $folder->getName(),
                'isFolder' => true
            ];

            foreach ($folder->getArticles() as $article) {
                $response['articles'][] = [
                    'label' => $article->getName(),
                    'value' => $article->getId(),
                ];
            }
        }

        $response['budget']['id'] = $this->em->getRepository('App\Entity\Budget')->findLastId();
        $actualDate = new \DateTime();
        $response['budget']['dateStamp'] = $actualDate->format('d/m/Y');

        $response['ownCompany'] = [
            'name' => $company->getName(),
            'taxIdentification' => $company->getTaxIdentification(),
            'address' => $company->getAddress(),
            'cp' => $company->getCp(),
            'city' => $company->getCity(),
            'phone' => $company->getPhone(),
            'email' => $company->getEmail(),
        ];

        $response['client'] = [];
        $response['clients'] = [];
        if ($data['clientID']) {
            $client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']);

            $response['client'] = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'surname' => $client->getSurname(),
                'email' => $client->getContactEmail(),
                'address' => [
                    'name' => $client->getAddress(),
                    'cp' => $client->getCp(),
                    'city' => $client->getCity(),
                ],
                'taxIdentification' => $client->getTaxIdentification(),
                'phone' => $client->getTlf()
            ];
        } else {
            $clients = $this->em->getRepository('App\Entity\Client')->findAll();
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
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
    #[Route('/get', name: 'api_budget_get', methods: ['POST'])]
    public function api_budget_get(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $budget = $this->em->getRepository('App\Entity\Budget')->findOneById($data['budgetID']);
        $client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']);
        if (!$budget || !$client)
            return new JsonResponse('Cliente o presupuesto no existe', Response::HTTP_NOT_FOUND);

        $company = $this->em->getRepository('App\Entity\Company')->findOneById(1);      

        $response['id'] = $budget->getId();
        $response['dateStamp'] = $budget->getDateTime()->format('d/m/Y');
        $response['title'] = $budget->getTitle();

        foreach ($budget->getBudgetArticles() as $article) {
            $response['articles'][] = [
                'code' => $article->getArticleCode(),
                'article' => $article->getNameArticle(),
                'quantity' => $article->getQuantity(),
                'price' => $article->getPrice(),
                'total' => $article->getTotal(),
            ];
        }

        $response['ownCompany'] = [
            'name' => $company->getName(),
            'taxIdentification' => $company->getTaxIdentification(),
            'address' => $company->getAddress(),
            'cp' => $company->getCp(),
            'city' => $company->getCity(),
            'phone' => $company->getPhone(),
            'email' => $company->getEmail(),
        ];

        $response['client'] = [
            'name' => $budget->getClientName(),
            // 'surname' => $client->getSurname(),
            'email' => $budget->getClientEmail(),
            'address' => [
                'name' => $budget->getClientAddress()['name'],
                'cp' => $budget->getClientAddress()['cp'],
                'city' => $budget->getClientAddress()['city'],
            ],
            'taxIdentification' => $budget->getClientTaxIdentification(),
            'phone' => $budget->getClientTlf()
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/client/get', name: 'api_budget_client_get', methods: ['POST'])]
    public function api_budget_client_get(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']);

        $response = [];
        $response = [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'surname' => $client->getSurname(),
            'email' => $client->getContactEmail(),
            'address' => [
                'name' => $client->getAddress(),
                'cp' => $client->getCp(),
                'city' => $client->getCity(),
            ],
            'taxIdentification' => $client->getTaxIdentification(),
            'phone' => $client->getTlf()
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/article/get', name: 'api_budget_article_get', methods: ['POST'])]
    public function api_budget_article_get(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $article = $this->em->getRepository('App\Entity\Article')->findOneById($data['articleID']);

        $response = [
            'name' => $article->getName(),
            'code' => $article->getCode(),
            'price' => $article->getPrice(),
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/add', name: 'api_budget_add', methods: ['PUT'])]
    public function api_budget_add(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);
        if (isset($data['payload']['client']['id']))
            $clientID = $data['payload']['client']['id'];
        else $clientID = $data['payload']['client'];
        $client = $this->em->getRepository('App\Entity\Client')->findOneById($clientID);

        $newBudget = new Budget;
        $dateTime = new \DateTime();

        $newBudget
            ->setDateTime($dateTime)
            ->setClient($client)
            ->setClientName($client->getName())
            ->setClientTaxIdentification($client->getTaxIdentification())
            ->setClientEmail($client->getContactEmail())
            ->setClientTlf($client->getTlf())
            ->setClientAddress(
                [
                    'name' => $client->getAddress(),
                    'cp' => $client->getCp(),
                    'city' => $client->getCity(),
                ]
            )
            ->setBudgetID($data['payload']['budgetID'])
            ->setTitle($data['payload']['title'])
            ->setIva($data['payload']['iva']);

        // #[ORM\Column(nullable: true)]
        // private ?float $subTotal = null;

        // #[ORM\Column(nullable: true)]
        // private ?float $total = null;

        $this->em->persist($newBudget);
        $this->em->flush();

        $subTotal = 0;
        foreach ($data['payload']['articles'] as $article) {
            $newArticle = new BudgetArticle;

            $newArticle
                ->setDateTime($dateTime)
                ->setArticleCode($article['code'])
                ->setNameArticle($article['article'])
                ->setQuantity($article['quantity'])
                ->setPrice($article['price'])
                ->setTotal($article['total'])
                ->setBudget($newBudget);
            $this->em->persist($newArticle);

            $subTotal =  $subTotal + $article['total'];
        }

        $newBudget
            ->setSubTotal($subTotal)
            ->setTotal(($subTotal * 0.21) + $subTotal);

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
