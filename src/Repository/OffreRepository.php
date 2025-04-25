<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offre>
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    //    /**
    //     * @return Offre[] Returns an array of Offre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Offre
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByTitre(string $search): array
    {
        $qb = $this->createQueryBuilder('o');

        if (!empty($search)) {
            $qb->andWhere('o.titre LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }


    public function findBySearchAndContractType(string $search = '', string $contractType = ''): array
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.date_publication', 'DESC');

        // Apply search filter if provided
        if (!empty($search)) {
            $qb->andWhere('o.titre LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Apply contract type filter if provided
        if (!empty($contractType)) {
            $qb->andWhere('o.type_contrat = :contractType')
                ->setParameter('contractType', $contractType);
        }

        return $qb->getQuery()->getResult();
    }


    public function findBySearchContractTypeAndSort(string $search = '', string $contractType = '', string $sortBy = ''): array
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.idOffre', 'DESC');

        // Apply search filter if provided
        if (!empty($search)) {
            $qb->andWhere('o.titre LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Apply contract type filter if provided
        if (!empty($contractType)) {
            $qb->andWhere('o.type_contrat = :contractType')
                ->setParameter('contractType', $contractType);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'date_pub_asc':
                $qb->orderBy('o.date_publication', 'ASC');
                break;
            case 'date_pub_desc':
                $qb->orderBy('o.date_publication', 'DESC');
                break;
            case 'date_lim_asc':
                $qb->orderBy('o.date_limite', 'ASC');
                break;
            case 'date_lim_desc':
                $qb->orderBy('o.date_limite', 'DESC');
                break;
            case 'salaire_asc':
                $qb->orderBy('o.salaire', 'ASC');
                break;
            case 'salaire_desc':
                $qb->orderBy('o.salaire', 'DESC');
                break;
            default:
                $qb->orderBy('o.date_publication', 'DESC'); // Default sort
        }

        return $qb->getQuery()->getResult();
    }
}
