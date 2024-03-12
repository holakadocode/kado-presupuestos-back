<?php

namespace App\Controller;

use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/provider')]

class ProviderController extends AbstractController
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

    #[Route('/list', name: 'api_provider_list', methods: ['GET'])]
    public function api_provider_list(): Response
    {
        $providers = $this->em->getRepository('App\Entity\Provider')->findAll();

        $response = [];

        foreach ($providers as $provider) {
            $response[] = [
                'id' => $provider->getId(),
                'codProvider' => $provider->getCodProvider(),
                'nameCompany' => $provider->getNameCompany(),
                'businessName' => $provider->getBusinessName(),
                'nif' => $provider->getNif(),
                'contactPerson' => $provider->getContactPerson(),
                'email' => $provider->getEmail(),
                'phone' => $provider->getPhone(),
                'address' => $provider->getAddress(),
                'city' => $provider->getCity(),
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/add', name: 'api_provider_Add', methods: ['PUT'])]
    public function api_provider_add(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        $newProvider = new Provider();
        $newProvider
            ->setCodProvider($data['codProvider'])
            ->setNameCompany($data['nameCompany'])
            ->setBusinessName($data['businessName'])
            ->setNif($data['nif'])
            ->setContactPerson($data['contactPerson'])
            ->setEmail($data['email'])
            ->setAddress($data['address'])
            ->setPhone($data['phone'])
            ->setCity($data['city']);


        $errors = $this->validator->validate($newProvider);

        if (count($errors) > 0) {
            return new JsonResponse('Validacion error', Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->em->persist($newProvider);
        $this->em->flush();

        return new JsonResponse('Provider add', Response::HTTP_OK);
    }

    #[Route('/edit', name: 'api_provider_edit', methods: ['POST'])]
    public function api_provider_edit(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$providerToEdit = $this->em->getRepository('App\Entity\Provider')->findOneById($data['providerID']))
            return new JsonResponse('Provider not found', Response::HTTP_NOT_FOUND);


        $providerToEdit
            ->setCodProvider($data['payload']['codProvider'])
            ->setNameCompany($data['payload']['nameCompany'])
            ->setBusinessName($data['payload']['businessName'])
            ->setNif($data['payload']['nif'])
            ->setContactPerson($data['payload']['contactPerson'])
            ->setEmail($data['payload']['email'])
            ->setPhone($data['payload']['phone'])
            ->setAddress($data['payload']['address'])
            ->setCity($data['payload']['city']);

        $this->em->flush();
        return new JsonResponse('Provider edited', Response::HTTP_OK);
    }

    #[Route('/delete', name: 'api_provider_delete', methods: ['DELETE'])]
    public function api_provider_delete(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$data || !isset($data['providerID'])) {
            return new JsonResponse('Invalid provider data', Response::HTTP_BAD_REQUEST);
        }

        $provider = $this->em->getRepository('App\Entity\Provider')->findOneById($data['providerID']);

        if (!$provider) {
            return new JsonResponse('Provider not found', Response::HTTP_NOT_FOUND);
        }

        //eliminar articulos antes del proveedor
        //por desarrollar

        $this->em->remove($provider);
        $this->em->flush();

        return new JsonResponse('Provider deleted', Response::HTTP_OK);
    }
}
