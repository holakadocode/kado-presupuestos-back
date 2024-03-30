<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\FamilyFolder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;


#[Route('/api/storage')]
class StorageController extends AbstractController
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

    #[Route('/list', name: 'api_storage_list', methods: ['GET'])]
    public function api_storage_list(): JsonResponse
    {
        $familyFolders = $this->em->getRepository('App\Entity\FamilyFolder')->findAll();

        $response = [];

        foreach ($familyFolders as $folder) {
            $articles = [];
            foreach ($folder->getArticles() as $article) {
                $articles[] = [
                    "id" => $article->getId(),
                    "name" => $article->getName(),
                ];
            }

            $response[] = [
                "id" => $folder->getId(),
                "name" => $folder->getName(),
                "articles" => $articles,
            ];
            
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/add', name: 'api_storage_add', methods: ['PUT'])]
    public function api_storage_add(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $newFolder = new FamilyFolder();
        $newFolder->setName($data["nombreCarpeta"]);

        $this->em->persist($newFolder);
        $this->em->flush();

        return new JsonResponse($data["nombreCarpeta"], Response::HTTP_OK);
    }

    #[Route('/articleAdd', name: 'api_article_add', methods: ['PUT'])]
    public function api_article_add(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $familyFolder = $this->em->getRepository('App/Entity/FamilyFolder')->findOneById($data['id']);
        $budgetArticle = $this->em->getRepository('App/Entity/BudgetArticle')->findOneById($data['budgetArticleId']);

        $newArticle = new Article();
        $newArticle->setFamilyFolder($familyFolder);
        $newArticle->setDateTime($data["date"]);
        $newArticle->setName($data["name"]);
        $newArticle->setDescription($data["description"]);
        $newArticle->setCode($data["code"]);
        $newArticle->setDistributorCode($data["distributorCode"]);
        $newArticle->setDistributorPrice($data["distributorPrice"]);
        $newArticle->setPrice($data["articlePrice"]);
        $newArticle->setBudgetArticle($budgetArticle);

        $this->em->persist($newArticle);
        $this->em->flush();

        return new JsonResponse($data["nombreCarpeta"], Response::HTTP_OK);
    }
}
