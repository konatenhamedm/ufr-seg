<?php

namespace App\Repository;

use App\Entity\CoefValeurNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CoefValeurNote>
 *
 * @method CoefValeurNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoefValeurNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoefValeurNote[]    findAll()
 * @method CoefValeurNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoefValeurNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoefValeurNote::class);
    }

//    /**
//     * @return CoefValeurNote[] Returns an array of CoefValeurNote objects
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

//    public function findOneBySomeField($value): ?CoefValeurNote
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
