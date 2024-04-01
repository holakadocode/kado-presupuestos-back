<?php

namespace App\Controller;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/admin')]

class AdminController extends AbstractController
{
    private $em;
    private $request;
    private $validator;
    private $passwordHasher;

    function __construct(
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack,
        ValidatorInterface $validatorInterface,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->em = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validatorInterface;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/list', name: 'api_admin_list', methods: ['GET'])]
    public function api_admin_list(): Response
    {
        $admins = $this->em->getRepository('App\Entity\Admin')->findAll();

        $response = [];

        foreach ($admins as $admin) {
            $response[] = [
                'id' => $admin->getId(),
                'name' => $admin->getName(),
                'surname' => $admin->getSurname(),
                'email' => $admin->getEmail(),
                'password' => $admin->getPassword(),
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/add', name: 'api_admin_Add', methods: ['PUT'])]
    public function api_admin_add(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        $newAdmin = new Admin();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $newAdmin,
            $data['payload']['password']
        );
        $newAdmin
            ->setDateTime(new \DateTime())
            ->setEmail($data['payload']['email'])
            ->setRoles([])
            ->setPassword($hashedPassword)
            ->setName($data['payload']['name'])
            ->setSurname($data['payload']['surname'])
            ->setSalt(0);

        $errors = $this->validator->validate($newAdmin);

        if (count($errors) > 0) {
            return new JsonResponse('Validacion error', Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->em->persist($newAdmin);
        $this->em->flush();

        return new JsonResponse('Admin add', Response::HTTP_OK);
    }

    #[Route('/edit', name: 'api_admin_edit', methods: ['POST'])]
    public function api_admin_edit(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$adminToEdit = $this->em->getRepository('App\Entity\Admin')->findOneById($data['adminID']))
            return new JsonResponse('Admin not found', Response::HTTP_NOT_FOUND);

        $adminToEdit
            ->setEmail($data['payload']['email'])
            ->setName($data['payload']['name'])
            ->setSurname($data['payload']['surname']);

        if ($data['payload']['password'] !== '') {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $adminToEdit,
                $data['payload']['password']
            );
            $adminToEdit->setPassword($hashedPassword);
        }

        $this->em->flush();
        return new JsonResponse('Admin edited', Response::HTTP_OK);
    }

    #[Route('/delete', name: 'api_admin_delete', methods: ['DELETE'])]
    public function api_admin_delete(): Response
    {
        $data = json_decode($this->request->getContent(), true);

        if (!$data || !isset($data['adminID'])) {
            return new JsonResponse('Invalid admin data', Response::HTTP_BAD_REQUEST);
        }

        $admin = $this->em->getRepository('App\Entity\Admin')->findOneById($data['adminID']);

        if (!$admin) {
            return new JsonResponse('Admin not found', Response::HTTP_NOT_FOUND);
        }


        $this->em->remove($admin);
        $this->em->flush();

        return new JsonResponse('Admin deleted', Response::HTTP_OK);
    }
}
