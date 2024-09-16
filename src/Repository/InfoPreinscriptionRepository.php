<?php

namespace App\Repository;

use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\InfoPreinscription;
use App\Entity\Niveau;
use App\Entity\Personne;
use App\Entity\Preinscription;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoPreinscription>
 *
 * @method InfoPreinscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoPreinscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoPreinscription[]    findAll()
 * @method InfoPreinscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoPreinscriptionRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoPreinscription::class);
    }
    public function add(InfoPreinscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getListeRecouvrementParEtudiant($niveau)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableInfo = $this->getTableName(InfoPreinscription::class, $em);
        $tablePreinscription = $this->getTableName(Preinscription::class, $em);
        $tableUser = $this->getTableName(Etudiant::class, $em);
        $tablePersonne = $this->getTableName(Personne::class, $em);
        $tableFiliere = $this->getTableName(Filiere::class, $em);
        $tableNiveau = $this->getTableName(Niveau::class, $em);

        //dd($dateDebut,$dateFin);

        if ($niveau != null) {
            $sql = <<<SQL
SELECT e.id AS _etudiant_id,p.nom,p.prenom,f.montant_preinscription,d.etat
FROM {$tablePreinscription} d
Left JOIN {$tableInfo} i ON i.preinscription_id = d.id
Inner JOIN {$tableUser} e ON e.id = d.etudiant_id
Inner JOIN {$tableNiveau} n ON n.id = d.niveau_id
Inner JOIN {$tablePersonne} p ON p.id = e.id
Inner JOIN {$tableFiliere} f ON f.id = n.filiere_id
WHERE  d.niveau_id = :niveau and d.etat  in ('attente_paiement','valide','paiement_confirmation')

SQL;
        } else {
            $sql = <<<SQL
SELECT e.id AS _etudiant_id,p.nom,p.prenom,f.montant_preinscription,d.etat
FROM {$tablePreinscription} d
Left JOIN {$tableInfo} i ON i.preinscription_id = d.id
Inner JOIN {$tableNiveau} n ON n.id = d.niveau_id
Inner JOIN {$tableUser} e ON e.id = d.etudiant_id
Inner JOIN {$tablePersonne} p ON p.id = e.id
Inner JOIN {$tableFiliere} f ON f.id = n.filiere_id
WHERE  d.etat  in ('attente_paiement','valide','paiement_confirmation')

SQL;
        }

        $params['niveau'] = $niveau;
        // $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAllAssociative();
    }

    public function searchResult($niveau, $caissiere, $dateDebut, $dateFin, $mode)
    {
        $sql = $this->createQueryBuilder('i')
            ->leftJoin('i.preinscription', 'p')
            ->join('p.niveau', 'niveau')
            ->leftJoin('p.caissiere', 'ca')
            ->join('niveau.filiere', 'filiere')
            ->join('niveau.responsable', 'res')
            ->join('p.etudiant', 'etudiant')
            ->leftJoin('i.modePaiement', 'mode')
            ->andWhere('p.etat = :etat')
            ->setParameter('etat', 'valide');

        if ($niveau  || $caissiere || $dateDebut || $dateFin || $mode) {

            if ($niveau != "null") {
                $sql->andWhere('niveau.id = :niveau')
                    ->setParameter('niveau', $niveau);
            }
            if ($mode != "null") {
                $sql->andWhere('mode.id = :mode')
                    ->setParameter('mode', $mode);
            }
            if ($caissiere != "null") {
                $sql->andWhere('ca.id = :caissiere')
                    ->setParameter('caissiere', $caissiere);
            }

            // dd($dateDebut);

            if ($dateDebut != null && $dateFin == null) {
                $truc = explode('-', str_replace("/", "-", $dateDebut));
                $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                $sql->andWhere('i.datePaiement = :dateDebut')
                    ->setParameter('dateDebut', $new_date_debut);
            }
            if ($dateFin != "null" && $dateDebut == "null") {

                $truc = explode('-', str_replace("/", "-", $dateFin));
                $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                $sql->andWhere('i.datePaiement  = :dateFin')
                    ->setParameter('dateFin', $new_date_fin);
            }
            if ($dateDebut != "null" && $dateFin != "null") {

                $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

                $truc = explode('-', str_replace("/", "-", $dateFin));
                $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                $sql->andWhere('i.datePaiement BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $new_date_debut)
                    ->setParameter("dateFin", $new_date_fin);
            }

            /*  if ($dateDebut != "null" && $dateFin == "null") {
                $sql->andWhere('i.datePaiement = :dateDebut')
                    ->setParameter('dateDebut', $dateDebut);
            }
            if ($dateFin != "null" && $dateDebut == "null") {
                $sql->andWhere('i.datePaiement  = :dateFin')
                    ->setParameter('dateFin', $dateFin);
            }
            if ($dateDebut != "null" && $dateFin != "null") {
                $sql->andWhere('i.datePaiement BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter("dateFin", $dateFin);
            } */
        }

        return $sql->getQuery()->getResult();
    }

    //    /**
    //     * @return InfoPreinscription[] Returns an array of InfoPreinscription objects
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

    //    public function findOneBySomeField($value): ?InfoPreinscription
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
