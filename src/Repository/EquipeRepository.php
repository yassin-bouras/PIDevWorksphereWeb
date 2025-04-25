<?php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    public function findByNomEquipe(string $nom): array
    {
        return $this->createQueryBuilder('e')
            ->where('LOWER(e.nom_equipe) LIKE LOWER(:nom)')
            ->setParameter('nom', '%'.$nom.'%')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithProjects(): array
{
    return $this->createQueryBuilder('e')
        ->leftJoin('e.projets', 'p')
        ->addSelect('p')
        ->getQuery()
        ->getResult();
}

public function searchTeamsAndProjects(string $searchTerm): array
{
    return $this->createQueryBuilder('e')
        ->leftJoin('e.projets', 'p')
        ->addSelect('p')
        ->where('LOWER(e.nom_equipe) LIKE LOWER(:searchTerm)')
        ->orWhere('LOWER(p.nom) LIKE LOWER(:searchTerm)')
        ->setParameter('searchTerm', '%'.$searchTerm.'%')
        ->getQuery()
        ->getResult();
}

public function findByNomEquipeQuery(string $nom)
{
    return $this->createQueryBuilder('e')
        ->where('LOWER(e.nom_equipe) LIKE LOWER(:nom)')
        ->setParameter('nom', '%'.$nom.'%')
        ->getQuery();
}

//    /**
//     * @return Equipe[] Returns an array of Equipe objects
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

//    public function findOneBySomeField($value): ?Equipe
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}