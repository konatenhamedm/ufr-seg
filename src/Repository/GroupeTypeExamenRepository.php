<?php

namespace App\Repository;

use App\Entity\GroupeTypeExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupeTypeExamen>
 *
 * @method GroupeTypeExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupeTypeExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupeTypeExamen[]    findAll()
 * @method GroupeTypeExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupeTypeExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupeTypeExamen::class);
    }

//    /**
//     * @return GroupeTypeExamen[] Returns an array of GroupeTypeExamen objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupeTypeExamen
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
