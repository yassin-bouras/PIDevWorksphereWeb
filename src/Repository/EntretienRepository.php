<?php

namespace App\Repository;

use App\Entity\Entretien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entretien>
 */
class EntretienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entretien::class);
    }

    public function findByEmployeeId(int $employeeId): array
{
    return $this->createQueryBuilder('e')
        ->andWhere('e.user = :id')
        ->setParameter('id', $employeeId)
        ->orderBy('e.date_entretien', 'DESC')
        ->getQuery()
        ->getResult();
}


public function findByEmployeeIdWithStatusTrue(int $employeeId): array
{
    return $this->createQueryBuilder('e')
        ->andWhere('e.user = :id')
        ->andWhere('e.status = :status')
        ->setParameter('id', $employeeId)
        ->setParameter('status', true)
        ->orderBy('e.date_entretien', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findEntretiensBetweenDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
{
    return $this->createQueryBuilder('e')
        ->where('e.date_entretien BETWEEN :start AND :end')
        ->setParameter('start', $startDate->format('Y-m-d'))
        ->setParameter('end', $endDate->format('Y-m-d'))
        ->orderBy('e.date_entretien', 'ASC')
        ->getQuery()
        ->getResult();
}


public function findByKeyword(string $keyword)
{
    $qb = $this->createQueryBuilder('e')
               ->where('e.titre LIKE :keyword')
               ->setParameter('keyword', '%'.$keyword.'%')
               ->getQuery();

    return $qb->getResult();
}


public function findByTitre(string $search): array
{
    return $this->createQueryBuilder('e')
        ->where('e.titre LIKE :search')
        ->andWhere('LOWER(e.titre) LIKE LOWER(:search)')
        ->setParameter('search', '%' . $search . '%')
        ->orderBy('e.date_entretien', 'DESC') 
        ->getQuery()
        ->getResult();
}



//    /**
//     * @return Entretien[] Returns an array of Entretien objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Entretien
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
