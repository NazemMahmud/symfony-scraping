<?php

namespace App\Controller;

use App\Entity\Company;
use App\Exceptions\ScrapeException;
use App\Requests\NewCompanyRequest;
use App\Services\ScrapeService;
use App\Traits\HttpResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;


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
     * @return JsonResponse
     */
    #[Route('/api/company/new', name: 'app_company_store', methods: ['POST'])]
    public function store(ManagerRegistry $doctrine, NewCompanyRequest $request): JsonResponse
    {
        try {

            $data = $request->getContent();
            $info = $this->scrapeService->scrapeCompanyInfo($data['registration_code']);


            $company = new Company();
            $company->setName($info['companyName']);
            $company->setRegiCode($info['code']);
            $company->setVat($info['vat']);
            $company->setAddress($info['address']);
            $company->setMobilePhone($info['mobilePhone']);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->success_response(['message' => 'Company created'], 201);
        } catch (BadRequestHttpException $ex) {
            return $this->error_response($ex->getMessage(), $ex->getStatusCode() ?? Response::HTTP_BAD_REQUEST);
        } catch (ScrapeException $scrapeEx) {
            return $this->error_response($scrapeEx->getMessage(), $scrapeEx->getStatusCode() ?? Response::HTTP_BAD_REQUEST);
        }
    }
}
