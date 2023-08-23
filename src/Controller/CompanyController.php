<?php

namespace App\Controller;

use App\Requests\NewCompanyRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\ApiException;


class CompanyController extends AbstractController
{
    #[Route('/company', name: 'app_company')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CompanyController.php',
        ]);
    }

    #[Route('/api/company/new', name: 'app_company_store', methods: ['POST'])]
    public function store(NewCompanyRequest $request, ValidatorInterface $validator): JsonResponse
    {
        $data =  json_decode($request->getContent(), true);

        // Validate data
        $task = new Task();
        $task->setTitle($data['title']); // Assuming 'title' is part of the payload

        $errors = $validator->validate($task);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new ApiException(400, json_encode($errorMessages));
        }

        // Valid data, persist entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Task created'], 201);

        return $this->json([
            'message' => 'Welcome to post controller!',
            'data' => $data,
            'html' => $htmlContent,
//            "content" => $crawler
        ]);
    }
}
