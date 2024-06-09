<?php

namespace App\Repository;

use App\Entity\Preinscription;
use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Promotion>
 *
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository
{
    private $em;
    private $user;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, Security $security)
    {
        parent::__construct($registry, Promotion::class);
        $this->em = $entityManager;
        $this->user = $security->getUser();
    }

    public  function findNiveauDisponible()
    {
        $qb = $this->em->createQueryBuilder();

        $linked = $qb->select('n.id', 'rl.id as rlid')
            ->from(Preinscription::class, 'rl')
            ->innerJoin('rl.promotion', 'n')
            ->innerJoin('rl.utilisateur', 'u')
            ->andWhere('u.id =:id')
            ->setParameter('id', $this->user->getId())
            ->getQuery()
            ->getResult();


        $qb2 = $this->createQueryBuilder('e');
        $qb2->select('e')
            ->where('e.id not IN (:id)')
            ->setParameter('id', $linked);

        return $qb2;
    }




    //    /**
    //     * @return Promotion[] Returns an array of Promotion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Promotion
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
