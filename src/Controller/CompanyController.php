<?php

namespace App\Controller;


use App\Requests\NewCompanyRequest;
use App\Requests\UpdateCompanyRequest;
use App\Services\ScrapeService;
use App\Traits\HttpResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Exceptions\DBException;
use App\Exceptions\ScrapeException;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CompanyRepository;

class CompanyController extends AbstractController
{
    use HttpResponse;

    public function __construct(
        protected ScrapeService $scrapeService,
        protected CompanyRepository $companyRepo
    )
    {}

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
    public function store(NewCompanyRequest $request): JsonResponse
    {
        try {
            // todo: check it
            $info = $this->scrapeService->scrapeCompanyInfo($request->getContent()['registration_code']);
            $this->companyRepo->addCompanyInfo($info);

            return $this->success_response(['message' => 'Company created'], 201);
        } catch (BadRequestHttpException $badReqEx) {
            return $this->error_response($badReqEx->getMessage(), $badReqEx->getStatusCode() ?? Response::HTTP_BAD_REQUEST);
        } catch (ScrapeException $scrapeEx) {
            return $this->error_response($scrapeEx->getMessage(), $scrapeEx->getStatusCode() ?? Response::HTTP_BAD_REQUEST);
        } catch (DBException $dbEx) {
            return $this->error_response($dbEx->getMessage(), $dbEx->getStatusCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }  catch (Exception $ex) {
            return $this->error_response($ex->getMessage(), $ex->getStatusCode() ?? Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/company/{id}', name: 'app_company_delete', methods: ['DELETE'])]
    public function deleteCompany(int $id): JsonResponse
    {
        if ($company = $this->companyRepo->findOneBy(['id' => $id, 'deleted_at' => null])) {
            $this->companyRepo->deleteCompany($company);
            return $this->success_response(['message' => 'Company successfully deleted']);
        }

        return $this->error_response('Company not found', Response::HTTP_NOT_FOUND);
    }


    #[Route('/api/company/{id}', name: 'app_company_update', methods: ['PUT', 'PATCH'])]
    public function updateCompany(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        try {
            if ($company = $this->companyRepo->findOneBy(['id' => $id, 'deleted_at' => null])) {
                $this->companyRepo->updateCompany($company, $request->getContent());

                return $this->success_response(['message' => 'Company updated']);
            }

            return $this->error_response('Company not found', Response::HTTP_NOT_FOUND);

        } catch (DBException $dbEx) {
            return $this->error_response($dbEx->getMessage(), $dbEx->getStatusCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }  catch (\Exception $ex) {
            return $this->error_response('Failed to update company', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
