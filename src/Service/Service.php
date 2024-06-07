<?php


namespace App\Service;

use App\Entity\AnneeScolaire;
use App\Entity\ArticleMagasin;
use App\Entity\Document;
use App\Entity\Echeancier;
use App\Entity\Etudiant;
use App\Entity\FraisInscription;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\LigneDocument;
use App\Entity\Mouvement;
use App\Entity\MoyenneMatiere;
use App\Entity\Preinscription;
use App\Entity\Sens;
use App\Entity\Sortie;
use App\Repository\AnneeScolaireRepository;
use App\Repository\ArticleMagasinRepository;
use App\Repository\ClasseRepository;
use App\Repository\CoursRepository;
use App\Repository\DocumentRepository;
use App\Repository\EcheancierRepository;
use App\Repository\FraisBlocRepository;
use App\Repository\FraisInscriptionRepository;
use App\Repository\InfoInscriptionRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LigneDocumentRepository;
use App\Repository\MatiereRepository;
use App\Repository\MatiereUeRepository;
use App\Repository\MoyenneMatiereRepository;
use App\Repository\NoteRepository;
use App\Repository\SemestreRepository;
use App\Repository\SessionRepository;
use App\Repository\UniteEnseignementRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Service
{
    private $em;
    private $repository;
    private $infoRepository;
    private $echeancierRepository;
    private $ligneDocument;
    protected $articleMagasinRepository;
    protected $moyenneMatiereRepository;
    protected $matiereUeRepository;
    protected $matiereRepository;
    protected $classeRepository;
    protected $semestreRepository;
    protected $sessionRepository;
    protected $noteRepository;
    protected $coursRepository;
    private $security;
    private $anneeScolaireRepository;
    private $inscriptionRepository;
    private $fraisInscriptionRepository;
    private $infoInscriptionRepository;
    private $fraisBlocRepository;
    private $ueRepository;


    public function __construct(
        EntityManagerInterface $em,
        DocumentRepository $documentRepository,
        InfoInscriptionRepository $infoInscriptionRepository,
        EcheancierRepository $echeancierRepository,
        Security $security,
        MoyenneMatiereRepository $moyenneMatiereRepository,
        MatiereUeRepository $matiereUeRepository,
        MatiereRepository $matiereRepository,
        ClasseRepository $classeRepository,
        SemestreRepository $semestreRepository,
        SessionRepository $sessionRepository,
        NoteRepository $noteRepository,
        CoursRepository $coursRepository,
        AnneeScolaireRepository $anneeScolaireRepository,
        InscriptionRepository $inscriptionRepository,
        FraisInscriptionRepository $fraisInscriptionRepository,
        FraisBlocRepository $fraisBlocRepository,
        UniteEnseignementRepository $uniteEnseignementRepository,
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->repository = $documentRepository;
        $this->infoRepository = $infoInscriptionRepository;
        $this->echeancierRepository = $echeancierRepository;
        $this->moyenneMatiereRepository = $moyenneMatiereRepository;
        $this->matiereUeRepository = $matiereUeRepository;
        $this->matiereRepository = $matiereRepository;
        $this->classeRepository = $classeRepository;
        $this->semestreRepository = $semestreRepository;
        $this->sessionRepository = $sessionRepository;
        $this->noteRepository = $noteRepository;
        $this->coursRepository = $coursRepository;
        $this->anneeScolaireRepository = $anneeScolaireRepository;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->fraisInscriptionRepository = $fraisInscriptionRepository;
        $this->infoInscriptionRepository = $infoInscriptionRepository;
        $this->fraisBlocRepository = $fraisBlocRepository;
        $this->ueRepository = $uniteEnseignementRepository;

        //$this->verifieIfFile2(15,2);
    }


    public  function  getUser()
    {
        return $this->security->getUser();
    }
    public function verifieIfFile($id, $employe)
    {
        $repo = $this->repository->getNombreLigne($id, $employe);
        // dd($repo);
        return $repo;
    }
    public function verifieIfFile2($id, $employe)
    {
        $repo = $this->ligneDocument->getLastFile($id, $employe);
        //dd($repo);
        return $repo;
    }



    /*     public function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Mouvement::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'MVT' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));

    } */

    /* public function miseAjourArticleMagasin($magasin,$article,$quantiteRecue, $sens = null,$magasinDestinataire = null)
    {

        if ($sens instanceof Sens) {
            $sens = $sens->getSens();
        }
        if ($magasinDestinataire != null) {

            $verificationMagasin = $this->articleMagasinRepository->findOneBy(array('article'=>$article,'magasin'=>$magasin));
            $verificationMagasinDestinataire = $this->articleMagasinRepository->findOneBy(array('article'=>$article,'magasin'=>$magasinDestinataire));
            if($verificationMagasin){
                $quantiteMagasin    = $verificationMagasin->getQuantite() + $quantiteRecue * (-1);

                if($verificationMagasinDestinataire){
                    $quantiteMagasinDestinataire    = $verificationMagasinDestinataire->getQuantite() + $quantiteRecue;

                    $verificationMagasin->setQuantite($quantiteMagasin);
                    $verificationMagasinDestinataire->setQuantite($quantiteMagasinDestinataire);
                    $this->em->persist($verificationMagasin);
                    $this->em->persist($verificationMagasinDestinataire);
                    $this->em->flush();
                }else{

                    $verificationMagasin->setQuantite($quantiteMagasin);
                    $newArticleMagasin = new ArticleMagasin();
                    $newArticleMagasin->setArticle($article);
                    $newArticleMagasin->setMagasin($magasinDestinataire);
                    $newArticleMagasin->setQuantite($quantiteRecue);
                    $newArticleMagasin->setSeuil(10);
                    $this->em->persist($verificationMagasin);
                    $this->em->persist($newArticleMagasin);
                    $this->em->flush();

                }




            }


        }else{
            $verification = $this->articleMagasinRepository->findOneBy(array('article'=>$article,'magasin'=>$magasin));
            if($verification){

                $quantieFinale   = $verification->getQuantite() + $sens * $quantiteRecue;

                $verification->setQuantite($quantieFinale);
                $this->em->persist($verification);
                $this->em->flush();
            }else{
                $newArticleMagasin = new ArticleMagasin();
                $newArticleMagasin->setArticle($article);
                $newArticleMagasin->setMagasin($magasin);

                $quantite   = $newArticleMagasin->getQuantite() + $sens * $quantiteRecue;

                $newArticleMagasin->setQuantite($quantite);
                $newArticleMagasin->setSeuil(10);
                $this->em->persist($newArticleMagasin);
                $this->em->flush();
            }
        }


    } */

    public function paiementInscriptionNew(Inscription $inscription, $data = [])
    {


        $paiement = new InfoInscription();
        $paiement->setUtilisateur($this->getUser());
        $paiement->setCode($inscription->getCode());
        $paiement->setDateValidation(new \DateTime());
        $paiement->setInscription($inscription);
        $paiement->setDatePaiement($data['date']);
        $paiement->setCaissiere($this->getUser());
        $paiement->setModePaiement($data['modePaiement']);
        $paiement->setMontant($data['montant']);
        $paiement->setTypeFrais($data['typeFrais']);
        // $paiement->setEchenacier($echeancier);
        if ($data['modePaiement']->getCode() == 'CHQ') {
            $paiement->setNumeroCheque($data['numeroCheque']);
            $paiement->setBanque($data['banque']);
            $paiement->setTireur($data['tireur']);
            $paiement->setContact($data['contact']);
            $paiement->setDateCheque($data['dateCheque']);
        }
        if ($data['modePaiement']->isConfirmation()) {
            $paiement->setEtat('attente_confirmation');
        } else {
            $paiement->setEtat('payer');
        }

        $this->em->persist($paiement);
        $this->em->flush();
        //dd((int)$this->infoRepository->getMontantInfoInscription($inscription));

        $sommeMontant = (int)$this->infoRepository->getMontantInfoInscription($inscription);

        $listeEcheanciers = $this->echeancierRepository->findAllEcheance($inscription->getId());


        foreach ($listeEcheanciers as $key => $echeancier) {

            if ($sommeMontant == 0) {
                break;    /* Vous pourriez aussi utiliser 'break 1;' ici. */
            }

            $totalPayer = (int)$echeancier->getTotaPayer();

            if ($sommeMontant >= $echeancier->getMontant()) {
                $echeancier->setTotaPayer((int)$echeancier->getMontant());
                $echeancier->setEtat('payer');
                $sommeMontant = $sommeMontant - (int)$echeancier->getMontant();
            } else {

                $echeancier->setTotaPayer($sommeMontant);
                $echeancier->setEtat('pas_payer');
                $sommeMontant = 0;
            }


            $this->em->persist($echeancier);
            $this->em->flush();
        }

        $inscription->setTotalPaye($this->infoRepository->getMontantInfoInscription($inscription));

        if ($inscription->getMontant() == $this->infoRepository->getMontantInfoInscription($inscription)) {

            $inscription->setEtat('solde');
        }
        $this->em->persist($inscription);
        $this->em->flush();
    }
    public function paiementInscriptionEdit(Inscription $inscription)
    {

        $sommeMontantReel = (int)$this->infoRepository->getMontantInfoInscription($inscription);
        $sommeMontant = (int)$this->infoRepository->getMontantInfoInscription($inscription);

        $listeEcheanciers = $this->echeancierRepository->findAllEcheance($inscription->getId());

        if ($sommeMontantReel == 0) {
            foreach ($listeEcheanciers as $key => $echeancier) {


                $echeancier->setTotaPayer('0');
                $echeancier->setEtat('pas_payer');
                $this->em->persist($echeancier);
                $this->em->flush();
            }
        } else {
            foreach ($listeEcheanciers as $key => $echeancier) {


                $totalPayer = (int)$echeancier->getTotaPayer();

                if ($sommeMontant >= $echeancier->getMontant()) {
                    $echeancier->setTotaPayer((int)$echeancier->getMontant());
                    $echeancier->setEtat('payer');
                    $sommeMontant = $sommeMontant - (int)$echeancier->getMontant();
                } else {

                    $echeancier->setTotaPayer($sommeMontant);
                    $echeancier->setEtat('pas_payer');
                    $sommeMontant = 0;
                }


                $this->em->persist($echeancier);
                $this->em->flush();
            }
        }



        $inscription->setTotalPaye($this->infoRepository->getMontantInfoInscription($inscription));

        if ($inscription->getMontant() == $this->infoRepository->getMontantInfoInscription($inscription)) {

            $inscription->setEtat('solde');
        }
        $this->em->persist($inscription);
        $this->em->flush();
    }
    private function numero($code)
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Inscription::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ($code . '-' . date("y") . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
    public function registerEcheancierAdmin($blocEcheanciers, $etudiant): bool
    {
        $somme = 0;
        $response = true;
        foreach ($blocEcheanciers as $key => $value) {


            foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                $somme += $echeancier->getMontant();
            }
            $verifExistenceInscription = $this->inscriptionRepository->findOneBy(['classe' => $value->getClasse(), 'etudiant' => $etudiant]);

            if ($verifExistenceInscription == null) {
                if ($somme == (int)$value->getTotal()) {

                    $inscription = new Inscription();

                    $inscription->setCaissiere($this->getUser());
                    $inscription->setMontant($value->getTotal());
                    $inscription->setPromotion($this->classeRepository->find($value->getClasse())->getPromotion());
                    $inscription->setCode($this->numero($this->classeRepository->find($value->getClasse())->getPromotion()->getNiveau()->getCode()));
                    $inscription->setClasse($value->getClasse());
                    $inscription->setCodeUtilisateur($this->getUser()->getEmail());
                    $inscription->setEtudiant($etudiant);
                    $inscription->setEtat('valide');
                    $inscription->setDateInscription($value->getDateInscription());
                    $inscription->setTotalPaye('0');
                    $this->inscriptionRepository->save($inscription, true);

                    $value->setInscription($inscription);

                    foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                        $echeancierReel = new Echeancier();
                        $echeancierReel->setDateCreation(new DateTime());
                        $echeancierReel->setEtat('pas_payer');
                        $echeancierReel->setInscription($inscription);
                        $echeancierReel->setMontant($echeancier->getMontant());
                        $echeancierReel->setTotaPayer('0');
                        $this->echeancierRepository->save($echeancierReel, true);
                    }

                    //dd($value->getFraisBlocs()->count());
                    foreach ($value->getFraisBlocs() as $key => $fraisItem) {
                        $frais = new FraisInscription();
                        $frais->setMontant($fraisItem->getMontant());
                        $frais->setInscription($inscription);
                        $frais->setTypeFrais($fraisItem->getTypeFrais());
                        $this->em->persist($frais);
                        $this->em->flush();
                    }
                    $response;
                } else {
                    $response = false;
                }
            }
        }

        return $response;
    }
    public function registerEcheancierAdminEdit($blocEcheanciers, $etudiant): string
    {
        $somme = 0;

        // somme pas egal
        //inscription a deja un echaneacier qui a eu des payements
        $response = '';
        foreach ($blocEcheanciers as $key => $value) {

            foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                $somme += $echeancier->getMontant();
            }
            $newInscription = $this->inscriptionRepository->findOneBy(['classe' => $value->getClasse(), 'etudiant' => $etudiant]);
            $newInscriptionCount = $this->inscriptionRepository->findBy(['classe' => $value->getClasse(), 'etudiant' => $etudiant]);
            $inscription = $this->inscriptionRepository->find($value->getInscription()->getId());
            //dd($inscription->getId());

            if ($newInscription) {
                //dd($inscription->getTotalPaye());
                if ($newInscription->getId()  != $inscription->getId()) {
                    if (count($newInscriptionCount) == 0) {
                        if ($somme == (int)$value->getTotal()) {

                            $inscription->setMontant($value->getTotal());
                            $inscription->setPromotion($this->classeRepository->find($value->getClasse())->getPromotion());
                            $inscription->setCode($this->numero($this->classeRepository->find($value->getClasse())->getPromotion()->getNiveau()->getCode()));
                            $inscription->setClasse($value->getClasse());

                            if ($inscription->getTotalPaye() == "0") {

                                $inscription->setTotalPaye('0');
                            }

                            $this->inscriptionRepository->save($inscription, true);

                            $versements = $this->infoInscriptionRepository->findOneBy(['inscription' => $inscription->getId()]);

                            if ($versements) {

                                foreach ($versements as $key => $versement) {
                                    $this->em->remove($versement);
                                    $this->em->flush();
                                }
                            }


                            $value->setInscription($inscription);
                            //dd($inscription->getTotalPaye());
                            if ($inscription->getTotalPaye() == "0") {

                                foreach ($inscription->getEcheanciers() as $key => $echeancierInscription) {
                                    $this->em->remove($echeancierInscription);
                                    $this->em->flush();
                                }


                                foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                                    $echeancierReel = new Echeancier();
                                    $echeancierReel->setDateCreation(new DateTime());
                                    $echeancierReel->setEtat('pas_payer');
                                    $echeancierReel->setInscription($inscription);
                                    $echeancierReel->setMontant($echeancier->getMontant());
                                    $echeancierReel->setTotaPayer('0');
                                    $this->echeancierRepository->save($echeancierReel, true);
                                }
                                if ($value->getFraisBlocs()) {

                                    foreach ($value->getFraisBlocs() as $key => $fraisItem) {
                                        $frais = new FraisInscription();
                                        $frais->setMontant((int)$fraisItem->getMontant());
                                        $frais->setInscription($inscription);
                                        $frais->setTypeFrais($fraisItem->getTypeFrais());
                                        $this->em->persist($frais);
                                        $this->em->flush();
                                    }
                                }
                                $response = 'bonneEgalite';
                            } else {
                                $response = 'bonneEgalitePresenceEcheancierPayer';
                            }
                        } else {
                            $response = 'pasEgalite';
                        }
                    } else {
                        $response = 'existe';
                    }
                } else {
                    $versements = $this->infoInscriptionRepository->findOneBy(['inscription' => $inscription->getId()]);
                    $frais_bloc = $this->fraisBlocRepository->findBy(['blocEcheancier' => $value->getId()]);
                    if ($versements) {

                        foreach ($versements as $key => $versement) {
                            $this->em->remove($versement);
                            $this->em->flush();
                        }
                    }
                    if ($somme == (int)$value->getTotal()) {
                        //dd('');
                        //if ($inscription->getTotalPaye() == "0") { TO DO a decommenter apres avoir tester



                        foreach ($inscription->getEcheanciers() as $key => $echeancierInscription) {
                            $this->em->remove($echeancierInscription);
                            $this->em->flush();
                        }
                        foreach ($inscription->getFraisInscriptions() as $key => $fraisInscription) {
                            $this->em->remove($fraisInscription);
                            $this->em->flush();
                        }
                        foreach ($frais_bloc as $key => $bloc) {
                            $this->em->remove($bloc);
                            $this->em->flush();
                        }

                        $inscription->setTotalPaye('0');
                        foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                            $echeancierReel = new Echeancier();
                            $echeancierReel->setDateCreation(new DateTime());
                            $echeancierReel->setEtat('pas_payer');
                            $echeancierReel->setInscription($inscription);
                            $echeancierReel->setMontant($echeancier->getMontant());
                            $echeancierReel->setTotaPayer('0');
                            $this->echeancierRepository->save($echeancierReel, true);
                        }

                        if ($value->getFraisBlocs()) {

                            foreach ($value->getFraisBlocs() as $key => $fraisItem) {
                                $frais = new FraisInscription();
                                $frais->setMontant($fraisItem->getMontant());
                                $frais->setInscription($inscription);
                                $frais->setTypeFrais($fraisItem->getTypeFrais());
                                $this->fraisInscriptionRepository->save($frais, true);
                            }
                        }

                        $response = 'bonneEgalite';
                        // } else {
                        $response = 'bonneEgalitePresenceEcheancierPayer';
                        //}
                    } else {
                        $response = 'pasEgalite';
                    }
                }
            } else {

                //dd(count($newInscriptionCount));
                if (count($newInscriptionCount) == 0) {
                    if ($somme == (int)$value->getTotal()) {
                        $inscription->setMontant($value->getTotal());
                        $inscription->setPromotion($this->classeRepository->find($value->getClasse())->getPromotion());
                        $inscription->setCode($this->numero($this->classeRepository->find($value->getClasse())->getPromotion()->getNiveau()->getCode()));
                        $inscription->setClasse($value->getClasse());
                        if ($inscription->getTotalPaye() == "0") {

                            $inscription->setTotalPaye('0');
                        }
                        $this->inscriptionRepository->save($inscription, true);

                        $value->setInscription($inscription);

                        if ($inscription->getTotalPaye() == "0") {

                            foreach ($inscription->getEcheanciers() as $key => $echeancierInscription) {
                                $this->em->remove($echeancierInscription);
                                $this->em->flush();
                            }

                            foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                                $echeancierReel = new Echeancier();
                                $echeancierReel->setDateCreation(new DateTime());
                                $echeancierReel->setEtat('pas_payer');
                                $echeancierReel->setInscription($inscription);
                                $echeancierReel->setMontant($echeancier->getMontant());
                                $echeancierReel->setTotaPayer('0');
                                $this->echeancierRepository->save($echeancierReel, true);
                            }
                            $response = 'bonneEgalite';
                        } else {
                            $response = 'bonneEgalitePresenceEcheancierPayer';
                        }
                    } else {
                        $response = 'pasEgalite';
                    }
                } else {
                    $response = 'existe';
                }
            }
        }

        return $response;
    }


    public function updateInscription($blocEcheanciers, Etudiant $etudiant): string
    {
        $sommeEcheancier = 0;
        $response = 'bonneEgalitePresenceEcheancierPayer';
        foreach ($blocEcheanciers as $key => $value) {

            foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                $sommeEcheancier += $echeancier->getMontant();
            }

            $inscription = $this->inscriptionRepository->findOneBy(['classe' => $value->getClasse(), 'etudiant' => $etudiant]);
            if ($this->fraisInscriptionRepository->findBy(['inscription' => $inscription])) {

                foreach ($inscription->getFraisInscriptions() as $key => $fraisInscription) {
                    $this->em->remove($fraisInscription);
                    $this->em->flush();
                }
            }
            if ($this->echeancierRepository->findBy(['inscription' => $inscription])) {

                foreach ($inscription->getEcheanciers() as $key => $echeancierInscription) {
                    $this->em->remove($echeancierInscription);
                    $this->em->flush();
                }
            }

            if ($this->infoInscriptionRepository->findBy(['inscription' => $inscription])) {

                foreach ($inscription->getInfoInscriptions() as $key => $infoInscription) {
                    $this->em->remove($infoInscription);
                    $this->em->flush();
                }
            }
            /*  foreach ($value->getFraisBlocs() as $key => $fraisBloc) {
                $this->em->remove($fraisBloc);
                $this->em->flush();
            } */


            if ($value->getFraisBlocs()) {

                foreach ($value->getFraisBlocs() as $key => $fraisItem) {
                    $frais = new FraisInscription();
                    $frais->setMontant((int)$fraisItem->getMontant());
                    $frais->setInscription($inscription);
                    $frais->setTypeFrais($fraisItem->getTypeFrais());
                    $this->fraisInscriptionRepository->save($frais, true);
                }
            }
            foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                $echeancierReel = new Echeancier();
                $echeancierReel->setDateCreation(new DateTime());
                $echeancierReel->setEtat('pas_payer');
                $echeancierReel->setInscription($inscription);
                $echeancierReel->setMontant($echeancier->getMontant());
                $echeancierReel->setTotaPayer('0');
                $this->echeancierRepository->save($echeancierReel, true);
            }


            $inscription->setTotalPaye('0');


            $this->em->persist($inscription);
            $this->em->flush();
            // $newInscriptionCount = $this->inscriptionRepository->findBy(['classe' => $value->getClasse(), 'etudiant' => $etudiant]);
            //$inscription = $this->inscriptionRepository->find($value->getInscription()->getId());



        }
        return $response;
    }

    public function registerEcheancierAdminAfterValidationEtudiant($blocEcheanciers, $etudiant, $inscription): bool
    {
        $somme = 0;
        $response = true;
        foreach ($blocEcheanciers as $key => $value) {

            foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                $somme += $echeancier->getMontant();
            }
            $verifExistenceInscription = $this->inscriptionRepository->find($inscription);

            if ($verifExistenceInscription) {
                if ($somme == (int)$value->getTotal()) {


                    $verifExistenceInscription->setClasse($value->getClasse());


                    $this->inscriptionRepository->save($verifExistenceInscription, true);

                    foreach ($value->getEcheancierProvisoires() as $key => $echeancier) {
                        $echeancierReel = new Echeancier();
                        $echeancierReel->setDateCreation(new DateTime());
                        $echeancierReel->setEtat('pas_payer');
                        $echeancierReel->setInscription($verifExistenceInscription);
                        $echeancierReel->setMontant($echeancier->getMontant());
                        $echeancierReel->setTotaPayer('0');
                        $this->echeancierRepository->save($echeancierReel, true);
                    }
                    $response;
                } else {
                    $response = false;
                }
            }
        }

        return $response;
    }


    public function gestionNotes($dataNotes, $groupeTypes, $data = [], $controleVefication, $controle): int
    {
        $compteIfNoteSuperieurMax = 0;
        foreach ($dataNotes as $key => $row) {
            $somme = 0;
            $coef = 0;
            foreach ($row->getValeurNotes() as $key1 => $value) {
                $nbreTour = 0;
                foreach ($groupeTypes as $key => $groupe) {
                    //$note = 0;
                    /*  if ($key1 == $key) {

                        $note = (int)$groupe->getCoef() == 10 ? $value->getNote() * 2 * (int)$groupe->getType()->getCoef() : $value->getNote() * (int)$groupe->getType()->getCoef();


                        $note2 = 
                        if ($value->getNote() > 10 && $groupe->getCoef() == 10) {
                            $compteIfNoteSuperieurMax++;
                        }
                    }
                    if ($groupe->getType())
                        $coef = $coef + (int)$groupe->getType()->getCoef(); */

                    if ((int)$groupe->getCoef() == 10)
                        $nbreTour += 0.5;
                    if ((int)$groupe->getCoef() == 20)
                        $nbreTour += 1;
                    if ((int)$groupe->getCoef() == 40)
                        $nbreTour += 2;
                }



                $somme = $somme + $value->getNote();
                // dd()

            }
            //dd($somme / ($coef / 2), $note, $coef);
            $moyenneEtudiant = $somme / $nbreTour;
            // $moyenneEtudiant = $somme / ($nbreTour == 1 ? $coef : $coef / 2);
            $row->setMoyenneMatiere($moyenneEtudiant);

            $moyenneMatiere = $this->moyenneMatiereRepository->findOneBy(['matiere' => $data['matiere'], 'etudiant' => $row->getEtudiant()]);
            if ($moyenneMatiere) {
                $moyenneMatiere->setMoyenne($moyenneEtudiant);
                $matiereUeValide = $this->matiereUeRepository->findOneBy(['matiere' => $data['matiere']]);
                $moyenneMatiere->setValide($moyenneEtudiant  >= $matiereUeValide->getMoyenneValidation() ? 'Oui' : 'Non');
                $this->em->persist($moyenneMatiere);
                $this->em->flush();
            } else {
                $newMoyenneMatiere = new MoyenneMatiere();

                $newMoyenneMatiere->setEtudiant($row->getEtudiant());
                $newMoyenneMatiere->setMatiere($this->matiereRepository->find($data['matiere']));
                $newMoyenneMatiere->setMoyenne($moyenneEtudiant);

                $matiereUeValide = $this->matiereUeRepository->findOneBy(['matiere' => $data['matiere']]);

                $newMoyenneMatiere->setValide('seelater');
                //$newMoyenneMatiere->setValide($moyenneEtudiant  >= $matiereUeValide->getMoyenneValidation() ? 'Oui' : 'Non'); TODO
                $newMoyenneMatiere->setUe($this->ueRepository->find($data['ue']));
                $this->em->persist($newMoyenneMatiere);
                $this->em->flush();
            }
        }

        if ($controleVefication) {

            //dd($controleVefication);
            $controleVefication->setCour($this->coursRepository->findOneBy(['classe' => $data['classe'], 'matiere' => $data['matiere'], 'anneeScolaire' => $this->semestreRepository->find($data['semestre'])->getAnneeScolaire()->getId()]));
            $controleVefication->setAnneeScolaire($this->semestreRepository->find($data['semestre'])->getAnneeScolaire());
            $controleVefication->setClasse($this->classeRepository->find($data['classe']));
            $controleVefication->setMatiere($this->matiereRepository->find($data['matiere']));
            $controleVefication->setUe($this->ueRepository->find($data['ue']));
            $controleVefication->setSemestre($this->semestreRepository->find($data['semestre']));
            $this->em->persist($controleVefication);
        } else {
            $controle->setCour($this->coursRepository->findOneBy(['classe' => $data['classe'], 'matiere' => $data['matiere'], 'anneeScolaire' => $this->semestreRepository->find($data['semestre'])->getAnneeScolaire()->getId()]));
            $controle->setAnneeScolaire($this->semestreRepository->find($data['semestre'])->getAnneeScolaire());
            $controle->setMatiere($this->matiereRepository->find($data['matiere']));
            $controle->setClasse($this->classeRepository->find($data['classe']));
            $controle->setUe($this->ueRepository->find($data['ue']));
            $controle->setSemestre($this->semestreRepository->find($data['semestre']));
            $this->em->persist($controle);
        }
        $this->em->flush();

        return $compteIfNoteSuperieurMax;
    }

    public function rangExposant($dataNotes,)
    {
        $tableau = [];

        foreach ($dataNotes as $allNotes) {

            $tableau[$allNotes->getEtudiant()->getId()] = (int)$allNotes->getMoyenneMatiere();
        }

        foreach ($dataNotes as  $allNotes) {

            foreach ($tableau as $key => $value) {

                // dd($key, $tableau[$allNotes->getEtudiant()->getId()] ==);
                if ($tableau[$allNotes->getEtudiant()->getId()] == $tableau[$key]) {
                    $rang = $this->Rangeleve($key, $tableau, count($tableau));

                    //je verifie si le existe deja afin de savoir sil est execo ou pas
                    $existeRang = $this->noteRepository->findBy(['controle' => $allNotes->getControle(), 'rang' => $rang]);
                    //dd($existeRang);

                    $note = $this->noteRepository->find($allNotes->getId());

                    if ($note) {
                        $note->setRang($rang);
                        if (count($existeRang) > 1) {
                            $note->setExposant("ex");
                        } else {
                            if ($rang == 1) {
                                $note->setExposant("er");
                            } else {
                                $note->setExposant("e");
                            }
                        }
                        $this->em->persist($note);
                        $this->em->flush();
                    } else {
                        if (count($existeRang) > 1) {
                            $allNotes->setExposant("ex");
                        } else {
                            if ($rang == 1) {
                                $allNotes->setExposant("er");
                            } else {
                                $allNotes->setExposant("e");
                            }
                        }
                    }
                }
            }

            /*  $existeRang = $noteRepository->findBy(['controle' => $allNotes->getControle(), 'rang' => $rang]);

                    if ($note) {
                        if (count($existeRang) > 1) {
                            $note->setExposant("ex");
                        } else {
                            if ($rang == 1) {
                                $note->setExposant("er");
                            } else {
                                $note->setExposant("e");
                            }
                        }
                        $entityManager->persist($note);
                        $entityManager->flush();
                    } */
        }
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
}
