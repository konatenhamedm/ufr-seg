<?php

namespace App\Repository;

use App\Entity\EcheancierNiveau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EcheancierNiveau>
 *
 * @method EcheancierNiveau|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcheancierNiveau|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcheancierNiveau[]    findAll()
 * @method EcheancierNiveau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcheancierNiveauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcheancierNiveau::class);
    }

//    /**
//     * @return EcheancierNiveau[] Returns an array of EcheancierNiveau objects
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

//    public function findOneBySomeField($value): ?EcheancierNiveau
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
