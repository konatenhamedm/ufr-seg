<?php

namespace App\Repository;

use App\Entity\MatiereUe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MatiereUe>
 *
 * @method MatiereUe|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatiereUe|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatiereUe[]    findAll()
 * @method MatiereUe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatiereUeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatiereUe::class);
    }

    public function add(MatiereUe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllMatiere($classe, $annee)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.uniteEnseignement', 'u')
            ->innerJoin('u.niveau', 'niveau')
            ->andWhere('niveau.anneeScolaire = :annee')
            ->setParameter('annee', $annee)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
    public function getUeMatiere($matiere, $ue): ?MatiereUe
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.uniteEnseignement', 'u')
            ->innerJoin('d.matiere', 'mat')
            ->andWhere('d.uniteEnseignement = :ue')
            ->andWhere('mat.id = :matiere')
            ->setParameter('ue', $ue)
            ->setParameter('matiere', $matiere)
            ->getQuery()
            ->getResult()[0];
    }
    public function getAllMatiereWithouLimit($classe, $annee)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.uniteEnseignement', 'u')
            ->innerJoin('u.niveau', 'niveau')
            ->andWhere('niveau.anneeScolaire = :annee')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return MatiereUe[] Returns an array of MatiereUe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MatiereUe
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
