<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 *
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function save(Inscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countInscriptionFordelete($etudiant)
    {
        return $this->createQueryBuilder('i')
            ->where('i.etudiant = :etudiant')
            ->andWhere('i.etat IN (:etat)')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('etat', ['examen_echoue', 'rejete', 'valide', 'solde'])
            ->getQuery()
            ->getResult();
    }

    public function getListeEtudiant($classe)
    {

        return $this->createQueryBuilder('i')
            ->innerJoin('i.niveau', 'n')
            ->innerJoin('i.etudiant', 'e')
            ->andWhere('i.classe = :classe')
            ->setParameter('classe', $classe)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function getListeEtudiantByClasse($classe)
    {

        return $this->createQueryBuilder('i')
            ->innerJoin('i.niveau', 'n')
            ->innerJoin('i.classe', 'c')
            ->innerJoin('i.etudiant', 'e')
            ->andWhere('c = :classe')
            ->setParameter('classe', $classe)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function getListeInscription($etudiant)
    {

        return $this->createQueryBuilder('i')
            ->innerJoin('i.niveau', 'n')
            ->innerJoin('i.classe', 'c')
            ->innerJoin('i.etudiant', 'e')
            ->andWhere('e = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('i.dateInscription', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function getListeEtudiantInscris()
    {

        return $this->createQueryBuilder('i')
            ->select('distinct(i.etudiant) etudiantId,e.nom')
            ->innerJoin('i.niveau', 'n')
            ->innerJoin('i.classe', 'c')
            ->innerJoin('i.etudiant', 'e')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getListeEtudiantByClasseImprime($classe)
    {

        return $this->createQueryBuilder('i')
            ->innerJoin('i.niveau', 'n')
            ->innerJoin('i.classe', 'c')
            ->innerJoin('i.etudiant', 'e')
            ->andWhere('c.id = :classe')
            ->setParameter('classe', $classe)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Inscription[] Returns an array of Inscription objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Inscription
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
