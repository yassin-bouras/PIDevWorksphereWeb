<?php

namespace App\Repository;

use App\Entity\Sponsor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sponsor>
 */
class SponsorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsor::class);
    }

    public function findBySearchAndBudget(string $searchTerm = null, string $budgetFilter = null): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if ($searchTerm) {
            $queryBuilder->andWhere('s.nomSponso LIKE :searchTerm OR s.prenomSponso LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($budgetFilter) {
            switch ($budgetFilter) {
                case 'moins_10000':
                    $queryBuilder->andWhere('s.budgetSponso < 10000');
                    break;
                case 'entre_10000_50000':
                    $queryBuilder->andWhere('s.budgetSponso >= 10000 AND s.budgetSponso <= 50000');
                    break;
                case 'plus_50000':
                    $queryBuilder->andWhere('s.budgetSponso > 50000');
                    break;
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}