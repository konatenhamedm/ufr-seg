<?php

namespace App\Repository;

use App\Entity\Controle;
use App\Entity\MoyenneMatiere;
use App\Entity\Semestre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MoyenneMatiere>
 *
 * @method MoyenneMatiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method MoyenneMatiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method MoyenneMatiere[]    findAll()
 * @method MoyenneMatiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoyenneMatiereRepository extends ServiceEntityRepository
{
    use TableInfoTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoyenneMatiere::class);
    }

    public function getMatieres( $ue,$etudiant){
        return $this->createQueryBuilder("m")
       /*  ->select("distinct(s.id)") */
        ->innerJoin("m.matiere","mat")
        ->andWhere("m.ue = :ue")
        ->andWhere("m.etudiant = :etudiant")
        ->setParameter("ue",$ue)
        ->setParameter("etudiant",$etudiant)
        ->getQuery()
        ->getResult();
    }
    public function getMatieresPv( $ue,$matiere,$etudiant): ?MoyenneMatiere
    {
        return $this->createQueryBuilder("m")
       /*  ->select("distinct(s.id)") */
        ->innerJoin("m.matiere","mat")
        ->andWhere("m.ue = :ue")
        ->andWhere("mat.id = :matiere")
        ->andWhere("m.etudiant = :etudiant")
        ->setParameter("ue",$ue)
        ->setParameter("matiere",$matiere)
        ->setParameter("etudiant",$etudiant)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function getSemestres( $classe,$etudiant)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $moyenneMatiere = $this->getTableName(MoyenneMatiere::class, $em);
        $controle = $this->getTableName(Controle::class, $em);
        $semestre = $this->getTableName(Semestre::class, $em);

        //dd($dateDebut,$dateFin);

       
            $sql = <<<SQL
SELECT  DISTINCT(s.id) as id,s.numero,s.libelle
FROM {$moyenneMatiere} m
JOIN {$controle} c ON c.ue_id = m.ue_id
JOIN {$semestre} s ON s.id = c.semestre_id
WHERE  m.etudiant_id = :etudiant  AND c.classe_id = :classe
ORDER BY  s.numero
SQL;
        


        $params['classe'] = $classe;
        $params['etudiant'] = $etudiant;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getSemestresByClasse( $classe)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $moyenneMatiere = $this->getTableName(MoyenneMatiere::class, $em);
        $controle = $this->getTableName(Controle::class, $em);
        $semestre = $this->getTableName(Semestre::class, $em);

        //dd($dateDebut,$dateFin);

       
            $sql = <<<SQL
SELECT  DISTINCT(s.id) as id,s.numero,s.libelle
FROM {$moyenneMatiere} m
JOIN {$controle} c ON c.ue_id = m.ue_id
JOIN {$semestre} s ON s.id = c.semestre_id
WHERE   c.classe_id = :classe
ORDER BY  s.numero
SQL;
        


        $params['classe'] = $classe;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }

//    /**
//     * @return MoyenneMatiere[] Returns an array of MoyenneMatiere objects
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

//    public function findOneBySomeField($value): ?MoyenneMatiere
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
