<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projet>
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }


    public function searchProjects(?string $nom, ?string $etat, ?string $nomEquipe): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.equipes', 'e') 
            ->addSelect('e');
    
        if (!empty($nom)) {
            $qb->andWhere('LOWER(p.nom) LIKE LOWER(:nom)')
               ->setParameter('nom', '%' . $nom . '%');
        }
    
        if (!empty($etat)) {
            $qb->andWhere('p.etat = :etat')
               ->setParameter('etat', $etat);
        }
    
        if (!empty($nomEquipe)) {
            $qb->andWhere('LOWER(e.nom_equipe) LIKE LOWER(:nom_equipe)')
               ->setParameter('nom_equipe', '%' . $nomEquipe . '%');
        }
    
        return $qb->getQuery()->getResult();
    }
    
//    /**
//     * @return Projet[] Returns an array of Projet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Projet
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}