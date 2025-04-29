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


    public function findTachesByUser($userId, $search = null, $statut = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.user = :userId')  
            ->setParameter('userId', $userId);
    
        if ($search) {
            $qb->andWhere('LOWER(t.titre) LIKE LOWER(:search)')
               ->setParameter('search', '%' . $search . '%');
        }
    
        if ($statut) {
            $qb->andWhere('t.statut = :statut')
               ->setParameter('statut', $statut);
        }
    
        return $qb->getQuery()->getResult();  
    }
    

    
}