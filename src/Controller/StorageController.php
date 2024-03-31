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
                    "description" => $article->getDescription(),
                    "code" => $article->getCode(),
                    "distributorCode" => $article->getDistributorCode(),
                    "distributorPrice" => $article->getDistributorPrice(),
                    "price" => $article->getPrice(),
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
        $newFolder->setName($data["payload"]["nameFolder"]);

        $this->em->persist($newFolder);
        $this->em->flush();

        return new JsonResponse('', Response::HTTP_OK);
    }

    #[Route('/articleAdd', name: 'api_article_add', methods: ['PUT'])]
    public function api_article_add(): JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $familyFolder = $this->em->getRepository('App/Entity/FamilyFolder')->findOneById($data['id']);

        $newArticle = new Article();
        $newArticle
        ->setFamilyFolder($familyFolder)
        ->setDateTime(new \DateTime())
        ->setName($data["name"])
        ->setDescription($data["description"])
        ->setCode($data["code"])
        ->setDistributorCode($data["distributorCode"])
        ->setDistributorPrice($data["distributorPrice"])
        ->setPrice($data["articlePrice"]);

        $this->em->persist($newArticle);
        $this->em->flush();

        return new JsonResponse("", Response::HTTP_OK);
    }

    #[Route('/edit', name: 'api_storage_edit', methods: ['POST'])]
    public function api_storage_edit(): Response
    {
        // FALLA
        $data = json_decode($this->request->getContent(), true);

        $articleToEdit = $this->em->getRepository('App/Entity/Article')->findOneById($data['articleID']);

        $articleToEdit
            ->setName($data["payload"]["name"])
            ->setDescription($data["payload"]["description"])
            ->setCode($data["payload"]["code"])
            ->setDistributorCode($data["payload"]["distributorCode"])
            ->setDistributorPrice($data["payload"]["distributorPrice"])
            ->setPrice($data["payload"]["articlePrice"]);

        $this->em->flush();

        return new JsonResponse('Client edited', Response::HTTP_OK);
    }

    #[Route('/delete', name: 'api_article_delete', methods: ['DELETE'])]
    public function api_article_delete(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        $article = $this->em->getRepository('App\Entity\Article')->findOneById($data['articleID']);
        if (!$article) {
            return new JsonResponse('Article not found', Response::HTTP_NOT_FOUND);
        }
        
        $this->em->remove($article);
        $this->em->flush();

        return new JsonResponse('', Response::HTTP_OK);
    }    
}
