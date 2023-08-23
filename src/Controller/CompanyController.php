<?php

namespace App\Controller;

use App\Requests\NewCompanyRequest;
use App\Services\ScrapeService;
use App\Traits\HttpResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exceptions\ApiException;


class CompanyController extends AbstractController
{
    use HttpResponse;

    public function __construct(protected ScrapeService $scrapeService)
    {
    }

    #[Route('/company', name: 'app_company')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CompanyController.php',
        ]);
    }

    /**
     * @param NewCompanyRequest $request
     * @throws ApiException
     * @return ApiException|JsonResponse
     */
    #[Route('/api/company/new', name: 'app_company_store', methods: ['POST'])]
    public function store(NewCompanyRequest $request)
    {
        try {

            $data = $request->getContent();
            $this->scrapeService->scrapeCompanyInfo('');

            // Validate data
//        $task = new Company();
//        $task->setTitle($data['title']); // Assuming 'title' is part of the payload

//        $errors = $validator->validate($task);
//        if (count($errors) > 0) {
//            $errorMessages = [];
//            foreach ($errors as $error) {
//                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
//            }
//            throw new ApiException(400, json_encode($errorMessages));
//        }
//
//        // Valid data, persist entity
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->persist($task);
//        $entityManager->flush();

            return new JsonResponse(['message' => 'Company created'], 201);
        } catch (BadRequestHttpException $ex) {
            return $this->error_response($ex->getMessage(), $ex->getStatusCode() ?? Response::HTTP_BAD_REQUEST);
        }

//        return $this->json([
//            'message' => 'Welcome to post controller!',
//            'data' => $data,
//            'html' => $htmlContent,
////            "content" => $crawler
//        ]);
    }
}
