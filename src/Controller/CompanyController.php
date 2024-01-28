<?php

namespace App\Controller;


use App\Exceptions\DBException;
use App\Exceptions\ScrapeException;
use App\Requests\NewCompanyRequest;
use App\Requests\UpdateCompanyRequest;
use App\Services\CacheService;
use App\Services\ScrapeService;
use App\Traits\HttpResponse;
use App\Repository\CompanyRepository;
use Exception;
use Snc\RedisBundle\Client\Predis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class CompanyController extends AbstractController
{
    use HttpResponse;

    public function __construct(
        protected ScrapeService $scrapeService,
        protected CompanyRepository $companyRepo,
        protected CacheService $cache
    )
    {}

    /**
     * api URL sample: {BASE_URL}/api/companies?page=2&perPage=7
     * for pagination optional parameters: page => to define data range, perPage => max number of data return
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/companies', name: 'app_company_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $queryParams = $request->query->all();
        $page = $queryParams['page'] ?? 1;
        $pageSize = $queryParams['perPage'] ?? 10;

        if ($this->scrapeService->checkCache( $this->cache, $page, $pageSize) ) {
            return $this->success_response(['data' => json_decode($this->cache->getData('company'))]);
        }

        $paginator = $this->companyRepo->getPaginatedData($page, $pageSize);

        if ($paginator->count()) {
            $companies = [];
            foreach ($paginator as $company) {
                $companies[] = [
                    'id' => $company->getId(),
                    'name' => $company->getName(),
                    'registration_code' => $company->getRegiCode(),
                    'vat' => $company->getVat(),
                    'address' => $company->getAddress(),
                    'mobile_phone' => $company->getMobilePhone(),
                ];
            }

            $data = [
                'items' => $companies,
                'pagination' => [
                    'total_items' => $paginator->count(),
                    'current_page' => (int)$page,
                    'last_page' => ceil($paginator->count() / $pageSize),
                    'has_previous_page' => $page > 1,
                    'has_next_page' => $paginator->count() > $pageSize * $page,
                ],
            ];

            $this->cache->cacheValues(['page' => $page, 'perPage' => $pageSize, 'company' => json_encode($data)]);
            return $this->success_response(['data' =>$data]);
        }

        return $this->success_response(['message' => 'No data found, insert some data']);
    }

    /**
     * if code exist in redis, then it is already stored, no need to scrape again, DB query for checking duplicate is reduced
     *
     * @param NewCompanyRequest $request
     * @return JsonResponse
     */
    #[Route('/api/company/new', name: 'app_company_store', methods: ['POST'])]
    public function store(NewCompanyRequest $request): JsonResponse
    {
        $code = $request->getContent()['registration_code'];

        try {
            if (in_array($code, $this->cache->getList( 'registration_codes'))) {
                throw new Exception('This code is already stored.', Response::HTTP_BAD_REQUEST);
            }

            $info = $this->scrapeService->scrapeCompanyInfo($request->getContent()['registration_code']);
            $this->companyRepo->addCompanyInfo($info);

            $this->cache->cacheList( 'registration_codes', $code);
            return $this->success_response(['message' => 'Company created'], 201);
        } catch (BadRequestHttpException $badReqEx) {
            return $this->error_response($badReqEx->getMessage(), $badReqEx->getStatusCode() ?? Response::HTTP_BAD_REQUEST);
        } catch (ScrapeException $scrapeEx) {
            return $this->error_response($scrapeEx->getMessage(), $scrapeEx->getStatusCode() ?? Response::HTTP_BAD_REQUEST);
        } catch (DBException $dbEx) {
            return $this->error_response($dbEx->getMessage(), $dbEx->getStatusCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }  catch (Exception $ex) {
            return $this->error_response($ex->getMessage(), $ex->getCode() ?? Response::HTTP_NOT_FOUND);
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


    /**
     * Update a company info
     * Check 1: company already exist or not, if not error
     * Must Check 2: if registration code is already used for other company. Because, user can't use other company's code for themselves. The code is unique
     *
     * @param UpdateCompanyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/company/{id}', name: 'app_company_update', methods: ['PUT', 'PATCH'])]
    public function updateCompany(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        // todo: when update clear redis
        try {
            if ($company = $this->companyRepo->findOneBy(['id' => $id, 'deleted_at' => null])) {
                $this->companyRepo->updateCompany($company, $request->getContent());
                $this->cache->deleteList(['page', 'perPage', 'company']);
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
