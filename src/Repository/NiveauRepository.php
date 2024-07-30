<?php

namespace App\Repository;

use App\Entity\Filiere;
use App\Entity\Niveau;
use App\Entity\NiveauEtudiant;
use App\Entity\Preinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Niveau>
 *
 * @method Niveau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Niveau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Niveau[]    findAll()
 * @method Niveau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NiveauRepository extends ServiceEntityRepository
{
    private $em;
    private $user;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, Security $security)
    {
        parent::__construct($registry, Niveau::class);
        $this->em = $entityManager;
        $this->user = $security->getUser();
    }

    public function add(NiveauEtudiant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findNiveauDisponible($anneeScolaire)
    {
        // Première requête
        $qb = $this->em->createQueryBuilder();
        $linked = $qb->select('n.id')
            ->from(Preinscription::class, 'rl')
            ->innerJoin('rl.niveau', 'n')
            ->innerJoin('rl.utilisateur', 'u')
            /*  ->andWhere("n.anneeScolaire = :annee") */
            ->andWhere('u.id = :id')
            ->setParameter('id', $this->user->getId())
            /* ->setParameter('annee', $anneeScolaire) */
            ->getQuery()
            ->getResult();

        // Extraction des ids du résultat
        $linkedIds = array_column($linked, 'id');

        // Deuxième requête
        $qb2 = $this->em->createQueryBuilder();
        $qb2->select('e')
            ->from(Niveau::class, 'e')
            ->innerJoin('e.anneeScolaire', 'an')
            ->andWhere("an.id = :anneedff")
            ->andWhere($qb2->expr()->notIn('e.id', ':ids'))
            ->setParameter('anneedff', $anneeScolaire) // en supposant que $anneeScolaire est le bon id
            ->setParameter('ids', $linkedIds);

        return $qb2;
    }



    //    /**
    //     * @return Niveau[] Returns an array of Niveau objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Niveau
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
