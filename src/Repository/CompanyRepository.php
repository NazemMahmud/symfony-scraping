<?php

namespace App\Repository;

use App\Entity\Company;
use App\Exceptions\DBException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(protected LoggerInterface $logger, ManagerRegistry $registry, )
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * add new company info
     * @param array $data
     * @return Company
     * @throws DBException
     * @return \Exception
     */
    public function addCompanyInfo(array $data): Company
    {
        try {
            $this->checkDuplicate($data['code']);

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
     * Check for unique registration_code entry
     * @param string $registrationCode
     * @return void
     * @throws DuplicateKeyException
     */
    private function checkDuplicate(string $registrationCode): void
    {
        if ($this->findOneBy(['regi_code' => $registrationCode])) {
            throw new DuplicateKeyException('The registration code is already in use.', Response::HTTP_CONFLICT);
        }
    }

    /**
     * Check for unique registration_code
     * @param Company $company
     * @return void
     */
    public function deleteCompany(Company $company)
    {
        $company->setSoftDelete(new \DateTime());
        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }
}
