<?php

namespace App\Repository;

use App\Entity\FraisBloc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FraisBloc>
 *
 * @method FraisBloc|null find($id, $lockMode = null, $lockVersion = null)
 * @method FraisBloc|null findOneBy(array $criteria, array $orderBy = null)
 * @method FraisBloc[]    findAll()
 * @method FraisBloc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FraisBlocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FraisBloc::class);
    }

//    /**
//     * @return FraisBloc[] Returns an array of FraisBloc objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FraisBloc
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
