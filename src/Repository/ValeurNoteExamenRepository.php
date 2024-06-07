<?php

namespace App\Repository;

use App\Entity\ValeurNoteExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValeurNoteExamen>
 *
 * @method ValeurNoteExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValeurNoteExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValeurNoteExamen[]    findAll()
 * @method ValeurNoteExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValeurNoteExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValeurNoteExamen::class);
    }

//    /**
//     * @return ValeurNoteExamen[] Returns an array of ValeurNoteExamen objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ValeurNoteExamen
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
