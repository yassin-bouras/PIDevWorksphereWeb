<?php

namespace App\Repository;

use App\Entity\Candidature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

use Doctrine\ORM\QueryBuilder;
/**
 * @extends ServiceEntityRepository<Candidature>
 */
class CandidatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidature::class);
    }

//    /**
//     * @return Candidature[] Returns an array of Candidature objects
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

//    public function findOneBySomeField($value): ?Candidature
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    // public function findByOffreTitre($titre): array
    // {
    //     $qb = $this->createQueryBuilder('c')
    //         ->innerJoin('c.offre', 'o')
    //         ->addSelect('o')
    //         ->where('o.titre LIKE :titre')
    //         ->setParameter('titre', '%' . $titre . '%')
    //         ->orderBy('c.id_candidature', 'ASC');

    //     return $qb->getQuery()->getResult();
    // }
    public function findByOffreTitre(string $search): array
{
    $qb = $this->createQueryBuilder('c')
        ->join('c.offre', 'o') // Join the related Offre entity
        ->addSelect('o');

    if (!empty($search)) {
        $qb->andWhere('o.titre LIKE :search')
           ->setParameter('search', '%' . $search . '%');
    }

    return $qb->getQuery()->getResult();
}

public function findByUserAndOffreTitre(User $user, string $search = ''): array
{
    $qb = $this->createQueryBuilder('c')
        ->andWhere('c.user = :user')
        ->setParameter('user', $user);

    if (!empty($search)) {
        $qb->join('c.offre', 'o')
           ->andWhere('o.titre LIKE :search')
           ->setParameter('search', '%' . $search . '%');
    }

    return $qb->getQuery()->getResult();
}


public function findByUser(User $user): array
{
    return $this->createQueryBuilder('c')
        ->andWhere('c.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}
}
