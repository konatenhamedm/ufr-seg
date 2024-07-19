<?php

namespace App\Repository;

use App\Entity\EvaluationValeurNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvaluationValeurNote>
 *
 * @method EvaluationValeurNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationValeurNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationValeurNote[]    findAll()
 * @method EvaluationValeurNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationValeurNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvaluationValeurNote::class);
    }

//    /**
//     * @return EvaluationValeurNote[] Returns an array of EvaluationValeurNote objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EvaluationValeurNote
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
