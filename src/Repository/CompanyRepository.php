<?php

namespace App\Repository;

use App\Entity\Company;
use App\Exceptions\DBException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

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
            $company = new Company();
            $company->setName($data['companyName']);
            $company->setRegiCode($data['code']);
            $company->setVat($data['vat']);
            $company->setAddress($data['address']);
            $company->setMobilePhone($data['mobilePhone']);

            $this->getEntityManager()->persist($company);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new DBException(message: 'Failed to store company data');
        }

        return $company;

//        $entityManager = $doctrine->getManager();
//        $entityManager->persist($company);
//        $entityManager->flush();
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
