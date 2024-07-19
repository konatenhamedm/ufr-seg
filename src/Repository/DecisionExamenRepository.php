<?php

namespace App\Repository;

use App\Entity\DecisionExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DecisionExamen>
 *
 * @method DecisionExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method DecisionExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method DecisionExamen[]    findAll()
 * @method DecisionExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DecisionExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DecisionExamen::class);
    }

//    /**
//     * @return DecisionExamen[] Returns an array of DecisionExamen objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DecisionExamen
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
