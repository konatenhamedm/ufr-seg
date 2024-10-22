<?php

namespace App\Service;

use App\Entity\Controle;
use App\Entity\Cours;
use App\Entity\EncartBac;
use App\Entity\GroupeType;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\Matiere;
use App\Entity\MatiereUe;
use App\Entity\MoyenneMatiere;
use App\Entity\Note;
use App\Entity\Preinscription;
use App\Entity\UniteEnseignement;
use App\Entity\ValeurNote;
use App\Repository\AnneeScolaireRepository;
use App\Repository\InscriptionRepository;
use App\Repository\MentionRepository;
use App\Repository\SemestreRepository;
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
    private $inscriptionRepo;
    private $semestreRepo;
   


    public function __construct(EntityManagerInterface $em,SemestreRepository $semestreRepo,InscriptionRepository $inscriptionRepo,MentionRepository $mentionRepository, AnneeScolaireRepository $anneeScolaireRepository, RequestStack $requestStack, RouterInterface $router, Security $security)
    {
        $this->em = $em;
        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
            $this->security = $security;
            $this->mentionRepository = $mentionRepository;
        }
        $this->anneeScolaireRepository = $anneeScolaireRepository;
        $this->inscriptionRepo = $inscriptionRepo;
        $this->semestreRepo = $semestreRepo;
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
    public function getAllMatiereByUeByEtudiantPV($ue,$matiere,$etudiant,)
    {

        return $this->em->getRepository(MoyenneMatiere::class)->getMatieresPv($ue,$matiere,$etudiant);
    }
    public function getRangInfo($controle,$etudiant)
    {

        return $this->em->getRepository(Note::class)->getRangInfo($controle,$etudiant);
    }
    public function getUeMatiere($matiere,$ue)
    {

        return $this->em->getRepository(MatiereUe::class)->getUeMatiere($matiere,$ue);
    }
    public function getUeMatierepv($ue)
    {

        return $this->em->getRepository(MatiereUe::class)->getUeMatierepv($ue);
    }
    public function getCoursMatiereClasse($matiere,$classe)
    {

        return $this->em->getRepository(Cours::class)->getCoursMatiereClasse($matiere,$classe);
    }
    public function getSizeUeEcue($ue,$semestre)
    {

        return $this->em->getRepository(UniteEnseignement::class)->find($ue);
    }

    public function getMatieresBySemestre($classe,$semestre)
    {
        return $this->em->getRepository(Controle::class)->findBy(['classe'=> $classe,'semestre'=> $semestre]);

    }
    public function getCours($classe,$matiere)
    {
        return $this->em->getRepository(Cours::class)->findOneBy(['classe'=> $classe,'matiere'=> $matiere]);

    }

    public function getNoteTotalByControle($controle,$classe){
        $inscription = $this->em->getRepository(Inscription::class)->findOneBy(['classe'=> $classe]);
        $note = $this->em->getRepository(Note::class)->findOneBy(['controle'=> $controle,'etudiant'=> $inscription->getEtudiant()]);
      $cpte = count($this->em->getRepository(ValeurNote::class)->findBy(['noteEntity'=> $note]));

        return $cpte;

    }
    public function getNoteTotalByControleGroupeType($controle){
        $groupes = $this->em->getRepository(GroupeType::class)->findBy(['controle'=> $controle]);
        
        return $groupes;

    }
    public function getNotesByControleClasseEtudiant($controle,$etudiant){
      
        $note = $this->em->getRepository(Note::class)->findOneBy(['controle'=> $controle,'etudiant'=> $etudiant]);
        $valeursNotes = $this->em->getRepository(ValeurNote::class)->findBy(['noteEntity'=> $note]);

        return $valeursNotes;

    }
    public function getAllNoteForExamen($ue,$etudiant){
      
        $allControles = $this->em->getRepository(Controle::class)->findBy(['ue'=> $ue]);
        $cpte = 0;
        foreach($allControles as $controle){
            $ecueData = $this->em->getRepository(MatiereUe::class)->findOneBy(['uniteEnseignement'=> $ue,'matiere'=> $controle->getMatiere()]);
            $note = $this->em->getRepository(Note::class)->findOneBy(['controle'=> $controle,'etudiant'=> $etudiant]);
            $valeursNotes = $this->em->getRepository(ValeurNote::class)->findBy(['noteEntity'=> $note]);
/* dd($valeursNotes); */
            foreach($valeursNotes as $valeursNote){
            if((int)$valeursNote->getCoefValeurNote()->getCoef() == 10 ){

                if($ecueData->getNoteEliminatoire() >= $valeursNote->getNote()*2){
                    $cpte = $cpte + 1;
                }
            }elseif((int)$valeursNote->getCoefValeurNote()->getCoef() == 20 ){
                if($ecueData->getNoteEliminatoire() >= $valeursNote->getNote()){
                    $cpte = $cpte + 1;
                }
            }else{
                if($ecueData->getNoteEliminatoire() >= $valeursNote->getNote()/2){
                    $cpte = $cpte + 1;
                }
            }
                
            
            }
           
        }
        
        return $cpte;

    }

    public function getMoyenneEliminatoire($ue,$matiere){
        return $this->em->getRepository(MatiereUe::class)->findOneBy(['uniteEnseignement'=> $ue,'matiere'=> $matiere])->getNoteEliminatoire();
    }
    public function getMoyenneMatiereEtudiant($controle,$etudiant){
      
        $note = $this->em->getRepository(Note::class)->findOneBy(['controle'=> $controle,'etudiant'=> $etudiant]);
    

        return $note;

    }

    public function getAllNote($ue,$matiere,$etudiant){
        $conrole =  $this->em->getRepository(Controle::class)->findOneBy(['ue'=> $ue,'matiere'=> $matiere]);
        $note =  $this->em->getRepository(Note::class)->findOneBy(['controle'=> $conrole,'etudiant'=> $etudiant]);
        $valeurNotes =  $this->em->getRepository(ValeurNote::class)->findBy(['noteEntity'=> $note]);

        return $valeurNotes;
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

    public function getRang($classe,$semestre,$etudiantId,$ueId,$type = true){

        // jerecupere les etudiants

        $tableau = [];
        $tableauP = [];


        $etudiants = $this->inscriptionRepo->findBy(array('classe'=> $classe));
        $ueClasses =  $this->em->getRepository(Controle::class)->getUe($classe,$semestre);

        foreach ($etudiants as $key => $etudiant) {

            $sommeT=0;
            $nbreT=0;
            foreach ($ueClasses as $key => $ueClasse) {
                $somme=0;
                $nbre=0;
                foreach ($this->em->getRepository(MoyenneMatiere::class)->getMatieres($ueClasse['ueId'],$etudiant->getEtudiant()->getId()) as $key => $mue) {
                    
                    $somme += $mue->getMoyenne();
                    $nbre ++;
                }
                $sommeT += $somme;
                $nbreT +=$nbre;
                $tableauP[$etudiant->getEtudiant()->getId().'-'.$ueClasse['ueId']] = $somme/$nbre;
            }

            $moyenne = $sommeT/$nbreT;
           $tableau[$etudiant->getEtudiant()->getId()] = $moyenne;
        }
    /* dd('', $tableauP); */

 /*    foreach ($tableau as $key => $value) {

        // dd($key, $tableau[$allNotes->getEtudiant()->getId()] ==);
        if ($tableau[$etudiantId .'-'.$ueId] == $tableau[$key]) {
            $rang = $this->Rangeleve($key, $tableau, count($tableau));
        
        }
    } */

    if($type){
        $rang = $this->Rangeleve($etudiantId.'-'.$ueId, $tableauP, count($tableauP));
    }else{
        $rang = $this->Rangeleve($etudiantId, $tableau, count($tableau));
    }
    if ($rang == 1) {
     $stringRang = $rang.' '.'er';
    } else {
        $stringRang = $rang.' '.'e';

    }


    return $stringRang;
    }

    function Rangeleve($case, $tab, $Nbr)
    {
        $rang = 1;

        foreach ($tab as $key => $value) {
            if ($value > $tab[$case]) {
                $rang = $rang + 1;
            }
        }
        /*   for ($i = 1; $i < $Nbr; $i++) {
            if ($tab[$i] > $tab[$case]) {
                $rang = $rang + 1;
            }
        } */
        return $rang;
    }

    public function getNoteTable($controle,$etudiant)
    {
        $noteTable = array();


        return $this->em->getRepository(Note::class)->getCoursMatiereClasse($matiere,$classe);
    }
}
