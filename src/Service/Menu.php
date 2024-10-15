<?php

namespace App\Service;

use App\Entity\Controle;
use App\Entity\Cours;
use App\Entity\EncartBac;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\MatiereUe;
use App\Entity\MoyenneMatiere;
use App\Entity\Note;
use App\Entity\Preinscription;
use App\Repository\AnneeScolaireRepository;
use App\Repository\MentionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use function PHPUnit\Framework\isEmpty;

class Menu
{

    private $em;
    private $route;
    private $container;
    private $security;

    private $resp;
    private $session;
    private $tableau = [];
    private $anneeScolaireRepository;
    private $mentionRepository;


    public function __construct(EntityManagerInterface $em,MentionRepository $mentionRepository, AnneeScolaireRepository $anneeScolaireRepository, RequestStack $requestStack, RouterInterface $router, Security $security)
    {
        $this->em = $em;
        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
            $this->security = $security;
            $this->mentionRepository = $mentionRepository;
        }
        $this->anneeScolaireRepository = $anneeScolaireRepository;
        //$this->session = $session;
    }
    public function getRoute()
    {
        return $this->route;
    }
    public function getListeAnnees()
    {
        return $this->anneeScolaireRepository->findAll();
    }

    public function getSomme($etudiant, $year, $niveau)
    {
        $data = $this->em->getRepository(InfoInscription::class)->rangeSommeParAnnneNiveauEtudiant($etudiant, $year, $niveau);
        return $data ? $data['somme'] : '';
    }
    public function getSommeTotal($etudiant, $niveau)
    {
        $data = $this->em->getRepository(InfoInscription::class)->sommeTotal($etudiant, $niveau);
        return $data ? $data['somme'] : 0;
    }

    public function nombrePreinscriptionEtudiant($etat, $utilisateur)
    {
        $repo = $this->em->getRepository(Preinscription::class)->nombrePreinscriptionEtudiant($etat, $utilisateur);
        return $repo;
    }
    public function nombrePreinscriptionAdmin($etat, $annee)
    {
        $repo = $this->em->getRepository(Preinscription::class)->nombrePreinscriptionAdmin($etat, $annee);
        return $repo;
    }
    public function getAllYears()
    {
        $repo = $this->em->getRepository(Preinscription::class)->listeAnneScolaire($this->security->getUser()->getPersonne());
        return $repo;
    }

    public function getAnnneeScolaire()
    {

        /*  if($this->session->get('anneeScolaire') == null) {

            $this->session->set('anneeScolaire', $this->anneeScolaireRepository->findOneBy(['actif' => 1]));
        } */
        return $this->anneeScolaireRepository->findOneBy(['actif' => 1]);
    }

    public function getEncartEtudiant($etudiantid)
    {

        return $this->em->getRepository(EncartBac::class)->getEncart($etudiantid)->getId();
    }
    public function getListeInscriptionByNiveau($etudiantid)
    {

        return $this->em->getRepository(Inscription::class)->getListeInscription($etudiantid);
    }
    public function getListeEtudiantByClasseImprime($classe)
    {

        return $this->em->getRepository(Inscription::class)->getListeEtudiantByClasseImprime($classe);
    }
    public function getEncoreByEtudiant($etudiant)
    {

        return $this->em->getRepository(EncartBac::class)->getEncoreByEtudiant($etudiant);
    }
    public function getAllUeByClasse($classe,$semestre)
    {

        return $this->em->getRepository(Controle::class)->getUe($classe,$semestre);
    }
    public function getAllMatiereByUeByEtudiant($ue,$etudiant)
    {

        return $this->em->getRepository(MoyenneMatiere::class)->getMatieres($ue,$etudiant);
    }
    public function getRangInfo($controle,$etudiant)
    {

        return $this->em->getRepository(Note::class)->getRangInfo($controle,$etudiant);
    }
    public function getUeMatiere($matiere,$ue)
    {

        return $this->em->getRepository(MatiereUe::class)->getUeMatiere($matiere,$ue);
    }
    public function getCoursMatiereClasse($matiere,$classe)
    {

        return $this->em->getRepository(Cours::class)->getCoursMatiereClasse($matiere,$classe);
    }
   
    function get_mention($moyenne) {
        $mentions = $this->mentionRepository->findAll();

        $mentionsResultat = [];
        foreach ($mentions as $mention) {
            $mentionsResultat["{$mention->getMoyenneMin()}-{$mention->getMoyenneMax()}"] = $mention->getLibelle();
        }
    
        foreach ($mentionsResultat as $range => $mention) {
            list($min, $max) = explode('-', $range);
            $min = (float)$min;
            $max = (float)$max;
    
            if ($moyenne >= $min && $moyenne < $max) {
                return $mention;
            }
        }
    
        return 'N/A';
    }
}
