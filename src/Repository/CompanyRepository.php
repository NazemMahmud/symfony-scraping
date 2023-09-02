<?php

namespace App\Repository;

use App\Entity\Company;
use App\Exceptions\DBException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\HttpFoundation\Response;


class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(protected LoggerInterface $logger, ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * add new company info
     * @param array $data
     * @return Company
     * @throws DBException
     * @throws \Exception
     */
    public function addCompanyInfo(array $data): Company
    {
        try {
            $company = new Company();
            $company->setName($data['companyName']);
            $company->setRegiCode($data['code']);
            $company->setVat($data['vat']);
            $company->setAddress($data['address']);
            $company->setMobilePhone($data['mobilePhone']);

            $this->getEntityManager()->persist($company);
            $this->getEntityManager()->flush();
        } catch (DuplicateKeyException $ex) {
            throw new DBException($ex->getCode(), $ex->getMessage());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new DBException(message: 'Failed to store company data');
        }

        return $company;
    }

    /**
     * soft delete a company
     *
     * @param Company $company
     * @return void
     */
    public function deleteCompany(Company $company)
    {
        $company->setSoftDelete(new \DateTime());
        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }

    /**
     * Update company information, for simplicity will work for PUT request
     *
     * @param Company $company
     * @return void
     */
    public function updateCompany(Company $company, array $data)
    {
            $criteria = new Criteria();
            $criteria->where(Criteria::expr()->eq('regi_code', $data['registration_code']));
            $criteria->andWhere(Criteria::expr()->neq('id',  $company->getId()));
            $matching = $this->matching($criteria);

            if(!$matching->isEmpty()) {
                throw new DBException( Response::HTTP_CONFLICT, 'The registration code is already in use.',);
            }

            $company->setName($data['name']);
            $company->setRegiCode($data['registration_code']);
            $company->setVat($data['vat']);
            $company->setAddress($data['address']);
            $company->setMobilePhone($data['mobile_phone']);

            $this->getEntityManager()->persist($company);
            $this->getEntityManager()->flush();
    }


    /**
     * Pagination & filtering
     * TODO: filtering
     *
     * @param int $page
     * @param int $pageSize
     * @return Paginator
     */
    public function getPaginatedData(int $page, int $pageSize): Paginator
    {
        $query =  $this->createQueryBuilder('c')
            ->andWhere('c.deleted_at IS NULL')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        return $paginator;
    }
}
