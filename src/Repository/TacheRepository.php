<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository

{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    public function findByProjet($projetId)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.projet = :projetId')
            ->setParameter('projetId', $projetId)
            ->getQuery()
            ->getResult();
    }
    
    public function findGroupedByProjet($projetId)
    {
        $taches = $this->findByProjet($projetId);
        
        return [
            'À faire' => array_filter($taches, fn($t) => $t->getStatut() === 'À faire'),
            'En cours' => array_filter($taches, fn($t) => $t->getStatut() === 'En cours'),
            'Terminé' => array_filter($taches, fn($t) => $t->getStatut() === 'Terminé'),
        ];
    }
}