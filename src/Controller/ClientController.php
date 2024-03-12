<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/client')]
class ClientController extends AbstractController
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

    // ADD

    #[Route('/add', name: 'api_client_add', methods: ['PUT'])]
    public function api_client_add(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        $newClient = new Client();
        $newClient
            ->setName($data['name'])
            ->setSurname($data['surname'])
            ->setContactEmail($data['contactEmail'])
            ->setAddress($data['address'])
            ->setCp(intval($data['cp']))
            ->setCity($data['city'])
            ->setTaxIdentification($data['taxIdentification'])
            ->setTlf($data['tlf'])
            ->setPrimaryKey($data['primaryKey'])
            ->setDateTime(new \DateTime());


        $errors = $this->validator->validate($newClient);

        if (count($errors) > 0) {
            return new JsonResponse('Validacion error', Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->em->persist($newClient);
        $this->em->flush();

        return new JsonResponse('Client add', Response::HTTP_OK);
    }


    // DELETE

    #[Route('/delete', name: 'api_client_delete', methods: ['DELETE'])]
    public function api_client_delete(): Response
    {

        $data = json_decode($this->request->getContent(), true);

        if (!$data || !isset($data['clientID'])) {
            return new JsonResponse('Invalid client data', Response::HTTP_BAD_REQUEST);
        }

        $client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']);

        if (!$client) {
            return new JsonResponse('Client not found', Response::HTTP_NOT_FOUND);
        }

        //eliminar presupuestos antes del cliente
        $budgets = $this->em->getRepository('App\Entity\Budget')->findBy(['client' => $client]);

        foreach ($budgets as $budget) {
            $this->em->remove($budget);
        }


        $this->em->remove($client);
        $this->em->flush();

        return new JsonResponse('Client deleted', Response::HTTP_OK);
    }

    // EDIT

    #[Route('/edit', name: 'api_client_edit', methods: ['POST'])]
    public function api_client_edit(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$clientToEdit = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']))
            return new JsonResponse('Client not found', Response::HTTP_NOT_FOUND);

        $clientToEdit
            ->setName($data['payload']['name'])
            ->setSurname($data['payload']['surname'])
            ->setContactEmail($data['payload']['contactEmail'])
            ->setAddress($data['payload']['address'])
            ->setCp($data['payload']['cp'])
            ->setCity($data['payload']['city'])
            ->setTaxIdentification($data['payload']['taxIdentification'])
            ->setTlf($data['payload']['tlf'])
            ->setPrimaryKey($data['payload']['primaryKey'])
            ->setDateTime(new \DateTime());

        $this->em->flush();

        return new JsonResponse('Client edited', Response::HTTP_OK);
    }

    // READ

    #[Route('/list', name: 'api_client_list', methods: ['GET'])]
    public function api_client_list(): Response
    {
        $clients = $this->em->getRepository('App\Entity\Client')->findAll();

        $response = [];

        foreach ($clients as $client) {

            $response[] = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'surname' => $client->getSurname(),
                'contactEmail' => $client->getContactEmail(),
                'address' => $client->getAddress(),
                'cp' => $client->getCp(),
                'city' => $client->getCity(),
                'taxIdentification' => $client->getTaxIdentification(),
                'tlf' => $client->getTlf()
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/get', name: 'api_client_get', methods: ['GET'])]
    public function api_client_get(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']))
            return new JsonResponse('Client not found', Response::HTTP_NOT_FOUND);

        $client = $this->em->getRepository('App\Entity\Client')->findOneById($data['clientID']);


        $response = [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'surname' => $client->getSurname(),
            'contactEmail' => $client->getContactEmail(),
            'address' => $client->getAddress(),
            'cp' => $client->getCp(),
            'city' => $client->getCity(),
            'taxIdentification' => $client->getTaxIdentification(),
            'tlf' => $client->getTlf(),
            'budget' => []
        ];

        foreach ($client->getBudgets() as $budget) {
            $response['budget'][] = [
                'id' => $budget->getId(),
                'title' => $budget->getTitle(),
                'iva' => $budget->getIva(),
                'total' => $budget->getTotal(),
            ];
        }


        return new JsonResponse($response, Response::HTTP_OK);
    }
}
