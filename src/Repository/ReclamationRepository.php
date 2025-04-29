<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Find all reclamations by user
     *
     * @param int $userId id of the user
     *
     * @return array
     */
    /*******  7dae3fbd-b598-4835-bd45-f16aab157fe0  *******/
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.user', 'u')
            ->where('u.iduser = :userId') // Use the correct field name 'id'
            ->setParameter('userId', $userId) // Use the correct parameter name 'userId'
            ->getQuery()
            ->getResult();
    }
    /**
     * Get the response ID for a given reclamation
     *
     * @param int $reclamationId
     * @return int|null
     */
    public function findReponseIdByReclamation(int $reclamationId): ?int
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT rep.id_reponse
         FROM App\Entity\Reponse rep
         JOIN rep.reclamation r
         WHERE r.id_reclamation = :reclamationId'
        )->setParameter('reclamationId', $reclamationId);

        $result = $query->getOneOrNullResult();

        return $result ? $result['id_reponse'] : null;
    }



    //    /**
    //     * @return Reclamation[] Returns an array of Reclamation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reclamation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
