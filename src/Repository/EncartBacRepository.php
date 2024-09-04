<?php

namespace App\Repository;

use App\Entity\EncartBac;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EncartBac>
 *
 * @method EncartBac|null find($id, $lockMode = null, $lockVersion = null)
 * @method EncartBac|null findOneBy(array $criteria, array $orderBy = null)
 * @method EncartBac[]    findAll()
 * @method EncartBac[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncartBacRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EncartBac::class);
    }


    public function getEncart($etudiant): ?EncartBac
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.etudiant = :val')
            ->setParameter('val', $etudiant)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return EncartBac[] Returns an array of EncartBac objects
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

    //    public function findOneBySomeField($value): ?EncartBac
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
