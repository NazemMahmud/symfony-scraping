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
     * Check for unique registration_code
     * @param string $registrationCode
     * @return void
     */
    private function checkDuplicate(string $registrationCode)
    {
        if ($this->findOneBy(['regi_code' => $registrationCode])) {
            throw new DuplicateKeyException('The registration code is already in use.', Response::HTTP_CONFLICT);
        }
    }

//    /**
//     * @return Company[] Returns an array of Company objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Company
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
