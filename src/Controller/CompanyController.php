<?php

namespace App\Controller;


use App\Requests\NewCompanyRequest;
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

            $data = $request->getContent();
            $info = $this->scrapeService->scrapeCompanyInfo($data['registration_code']);
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

    /**
     * @Route("/api/companies/{id}", name="delete_company", methods={"DELETE"})
     */
    public function deleteCompany(Company $company): JsonResponse
    {
        try {
            $this->getDoctrine()->getManager()->remove($company);
            $this->getDoctrine()->getManager()->flush();

            return $this->success_response(['message' => 'Company deleted']);
        } catch (\Exception $ex) {
            return $this->error_response('Failed to delete company', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/companies/{id}", name="update_company", methods={"PUT"})
     */
    public function updateCompany(Request $request, Company $company): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Update entity properties based on incoming data
            $company->setName($data['name']);
            $company->setVat($data['vat']);
            $company->setAddress($data['address']);
            // ... other properties

            $this->getDoctrine()->getManager()->flush();

            return $this->success_response(['message' => 'Company updated']);
        } catch (\Exception $ex) {
            return $this->error_response('Failed to update company', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
