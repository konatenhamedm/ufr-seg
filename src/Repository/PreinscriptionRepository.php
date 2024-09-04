<?php

namespace App\Repository;

use App\Entity\Deliberation;
use App\Entity\DeliberationPreinscription;
use App\Entity\Examen;
use App\Entity\NiveauEtudiant;
use App\Entity\Preinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Preinscription>
 *
 * @method Preinscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preinscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preinscription[]    findAll()
 * @method Preinscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreinscriptionRepository extends ServiceEntityRepository
{

    private $user;
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Preinscription::class);
        $this->user = $security->getUser();
    }
    public function add(Preinscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastRecord()
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.utilisateur', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $this->user->getId())
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getListeEtudiantExamen($niveau, $anneeScolaire)
    {
        //dd($niveau);
        if ($niveau != "null") {
            return $this->createQueryBuilder('p')
                ->innerJoin('p.etudiant', 'u')
                ->innerJoin('p.niveau', 'niveau')
                ->andWhere('p.etatDeliberation = :etatDeliberation')
                ->andWhere('p.etat = :etat')
                ->andWhere('p.niveau = :niveau')
                ->andWhere('niveau.anneeScolaire = :anneeScolaire')
                ->setParameter('niveau', $niveau)
                ->setParameter('etatDeliberation', 'pas_deliberer')
                ->setParameter('etat', 'valide')
                ->setParameter('anneeScolaire', $anneeScolaire)
                ->orderBy('u.nom', 'ASC')
                ->getQuery()
                ->getResult();

            if ($anneeScolaire != null) {

                $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                    ->setParameter('anneeScolaire', $anneeScolaire);
            }
        } else {

            // dd('');
            return $this->createQueryBuilder('p')
                ->innerJoin('p.etudiant', 'u')
                ->innerJoin('p.niveau', 'niveau')
                ->andWhere('p.etatDeliberation = :etatDeliberation')
                ->andWhere('p.etat = :etat')
                ->andWhere('niveau.anneeScolaire = :anneeScolaire')
                ->setParameter('etatDeliberation', 'pas_deliberer')
                ->setParameter('etat', 'valide')
                ->setParameter('anneeScolaire', $anneeScolaire)
                ->orderBy('u.nom', 'ASC')
                ->getQuery()
                ->getResult();
        }
    }
    public function dataFilireWithoutExamen($etudiant)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin('p.niveau', 'n')
            ->innerJoin('n.filiere', 'f')
            ->andWhere('p.etudiant = :etudiant')
            ->andWhere('p.etat = :status')
            ->andWhere('n.passageExamen = :etat')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('status', 'attente_paiement')
            ->setParameter('etat', 1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     */
    public function withoutDeliberation(Examen $examen)
    {
        $qb = $this->createQueryBuilder('p');
        $qbExists = $this->getEntityManager()->createQueryBuilder('a');
        $stmtExists = $qbExists->select('d')->from(Deliberation::class, 'd')
            ->join(DeliberationPreinscription::class, 'dl', 'WITH', 'dl.deliberation = d.id')
            ->andWhere('dl.preinscription = p.id')
            ->andWhere('d.examen = :examen');

        $qb->select('p')
            ->andWhere($qb->expr()->not($qb->expr()->exists($stmtExists->getDQL())))
            ->andWhere('p.niveau = :niveau')
            ->andWhere('p.etat = :etat')
            ->andWhere('p.etatDeliberation = :deliberation')
            ->setParameter('niveau', $examen->getNiveau())
            ->setParameter('examen', $examen)
            ->setParameter('etat', 'valide')
            ->setParameter('deliberation', 'pas_deliberer');

        return $qb;
    }


    public function nombrePreinscriptionEtudiant($etat, $utilisateur)
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.id)')
            ->join('d.utilisateur', 'u')
            ->join('d.etudiant', 'e')
            ->andWhere('u =:utilisateur')
            ->andWhere("d.etat =:etat")
            ->setParameter('etat', $etat)
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function nombrePreinscriptionAdmin($etat, $anneeScolaire = null)
    {
        $sql = $this->createQueryBuilder('d')
            ->select('count(d.id)');

        if ($etat != 'all') {
            $sql
                ->join('d.etudiant', 'e')
                ->andWhere('e.etat =:etatEtudiant')
                ->andWhere("d.etat =:etat")
                ->setParameter('etatEtudiant', 'complete')
                ->setParameter('etat', $etat);
        } else {
            $sql
                ->join('d.etudiant', 'e')
                ->andWhere('e.etat != :etatEtudiant')
                ->setParameter('etatEtudiant', 'complete');
        }

        if ($anneeScolaire != null) {

            $sql
                ->join('d.niveau', 'niveau')
                ->join('niveau.anneeScolaire', 'anneeScolaire')
                ->andWhere('anneeScolaire.id = :anneeScolaire')
                ->setParameter('anneeScolaire', $anneeScolaire);
        }

        return  $sql->getQuery()
            ->getSingleScalarResult();
    }

    public function listeAnneScolaire($etudiant)
    {
        return $this->createQueryBuilder('p')
            ->select("distinct(a.id) id,a.libelle")
            ->innerJoin('p.niveau', 'niveau')
            ->innerJoin('niveau.anneeScolaire', 'a')
            ->andWhere('p.etudiant = :etudiant')
            ->setParameter("etudiant", $etudiant)
            ->orderBy("a.id")
            ->getQuery()
            ->getResult();
    }
    public function getPreinscriptionNewInscription($etudiant): ?Preinscription
    {
        return $this->createQueryBuilder('p')
            ->where('p.etudiant = :etudiant')
            ->andWhere('p.etat IN (:etat)')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('etat', ['attente_paiement', 'attente_validation'])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }



    //    /**
    //     * @return Preinscription[] Returns an array of Preinscription objects
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

    //    public function findOneBySomeField($value): ?Preinscription
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
