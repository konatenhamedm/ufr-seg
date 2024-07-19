<?php

namespace App\Repository;

use App\Entity\ControleExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ControleExamen>
 *
 * @method ControleExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControleExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControleExamen[]    findAll()
 * @method ControleExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControleExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControleExamen::class);
    }

//    /**
//     * @return ControleExamen[] Returns an array of ControleExamen objects
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

//    public function findOneBySomeField($value): ?ControleExamen
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
