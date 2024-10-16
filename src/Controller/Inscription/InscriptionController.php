<?php

namespace App\Controller\Inscription;

use App\Controller\FileTrait;
use App\Entity\BlocEcheancier;
use App\Entity\EcheancierProvisoire;
use App\Entity\Etudiant;
use App\Entity\InfoInscription;
use App\Entity\InfoPreinscription;
use App\Entity\Inscription;
use App\Entity\Paiement;
use App\Entity\Preinscription;
use App\Entity\Versement;
use App\Form\EtudiantAdminNewType;
use App\Form\InscriptionAdminType;
use App\Form\InscriptionAffectationClasseType;
use App\Form\InscriptionEcheancierNewType;
use App\Form\InscriptionPayementType;
use App\Form\InscriptionRejeterType;
use App\Form\InscriptionType;
use App\Repository\ClasseRepository;
use App\Repository\EcheancierNiveauRepository;
use App\Repository\EcheancierRepository;
use App\Repository\FraisInscriptionRepository;
use App\Repository\FraisRepository;
use App\Repository\InfoInscriptionRepository;
use App\Repository\InfoPreinscriptionRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NaturePaiementRepository;
use App\Repository\PreinscriptionRepository;
use App\Repository\TypeFraisRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
use App\Service\Service;
use DateTime;
use DeepCopy\Filter\Filter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Registry;

#[Route('/admin/inscription/inscription')]
class InscriptionController extends AbstractController
{
    protected $workflow;
    use FileTrait;

    protected const UPLOAD_PATH = 'media_etudiant';


    public function __construct(Registry $workflow)
    {

        $this->workflow = $workflow;
    }

    #[Route('/{id}/imprime', name: 'app_comptabilite_inscription_print', methods: ['GET'])]
    public function imprimer($id, InfoInscription $inscription, EcheancierRepository $echeancierRepository): Response
    {
        //dd($echeancierRepository->findAllEcheanceDateFirst($id));
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'test.png';
        return $this->renderPdf("inscription/inscription/recu.html.twig", [
            'data' => $inscription->getInscription(),
            'info' => $inscription,
            'nombre' => count($inscription->getInscription()->getEcheanciers()),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'p',
            'protected' => true,

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' =>  $imgFiligrame,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }
    #[Route('/{id}/imprime/etudiant', name: 'app_comptabilite_inscription_print_etudiant', methods: ['GET'])]
    public function imprimerEtudiant(Inscription $inscription, EcheancierRepository $echeancierRepository): Response
    {
        //dd($echeancierRepository->findAllEcheanceDateFirst($id));
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'test.png';
        return $this->renderPdf("inscription/inscription/recu_etudiant.html.twig", [
            'data' => $inscription,
            'info' => $inscription->getInfoInscriptions(),
            'nombre' => count($inscription->getEcheanciers()),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'p',
            'protected' => true,

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' =>  $imgFiligrame,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }
    #[Route('/{id}/imprime/seconde', name: 'app_comptabilite_inscription_print_seconde', methods: ['GET'])]
    public function imprimerSecondeExemple($id, Inscription $inscription, EcheancierRepository $echeancierRepository): Response
    {
        //dd($echeancierRepository->findAllEcheanceDateFirst($id));
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'test.png';
        return $this->renderPdf("inscription/inscription/recu_seconde.html.twig", [
            'data' => $inscription,
            'info' => $echeancierRepository->findAllEcheanceDateFirst($id),
            'nombre' => count($inscription->getEcheanciers()),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'p',
            'protected' => true,

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' =>  $imgFiligrame,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }
    #[Route('/{id}/imprime/attente_confirmation', name: 'app_comptabilite_inscription_print_attente_confirmation', methods: ['GET'])]
    public function imprimeAttenteConfirmation($id, Inscription $inscription, EcheancierRepository $echeancierRepository): Response
    {

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'test.png';
        return $this->renderPdf("inscription/inscription/recu_attente_confirmation.html.twig", [
            'data' => $inscription,
            'info' => $echeancierRepository->findAllEcheanceDateFirst($id),
            'nombre' => count($inscription->getEcheanciers()),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
            'protected' => true,

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' =>  $imgFiligrame,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    #[Route('/{etat}/{id}', name: 'app_inscription_inscription_suivi_formation_index', methods: ['GET', 'POST'])]
    public function indexSuviFormation(Request $request, UserInterface $user, $id, string $etat, DataTableFactory $dataTableFactory, SessionInterface $session): Response
    {

        //dd($user->getPersonne());

        $anneeScolaire = $session->get('anneeScolaire');

        //dd($etat);
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');

        $titre = '';
        if ($etat == "echeance_soumis") {
            $titre = "Liste des inscriptions | validation des échéances";
        } elseif ($etat == "valide") {
            $titre = "Liste des inscriptions en attente de paiement";
        } elseif ($etat == "rejete") {
            $titre = "Liste des inscriptions rejettées | en attente d'écheancier";
        } elseif ($etat == 'attente_echeance') {
            $titre = "Liste des inscriptions en attente d'écheancier";
        } else {
            $titre = "Liste des inscriptions soldées";
        }
        //Code Etudiant, Filière, Nom et prénoms, Coût formation, Total payé, Solde


        if ($etat == 'valide') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);

            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }

            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return   abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }]);
        } else {

            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);
            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.montant']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return    abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }]);
        }



        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat, $id, $anneeScolaire) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c')
                    ->andWhere('p.id = :id')
                    ->setParameter('id', $id);
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
                if ($etat == 'attente_echeance') {
                    $qb->andWhere('p.etat = :etat')
                        ->orWhere('p.etat = :etat2')
                        ->setParameter('etat', 'attente_echeancier')
                        ->setParameter('etat2', 'rejete');
                } else {
                    $qb->andWhere('p.etat = :etat')
                        ->setParameter('etat', $etat);
                }

                if ($anneeScolaire) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_suivi_formation' . $etat  . $id);

        $renders = [
            'edit_etudiant' => new ActionRender(fn() => $etat == 'attente_echeance' || $etat == 'rejete'),
            'edit' => new ActionRender(fn() => $etat == 'echeance_soumis'),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'confirmation' => new ActionRender(function () use ($etat) {
                return $etat == 'valide';
            }),
            'payer' => new ActionRender(fn() => $etat == 'valide' && $isEtudiant == false),

        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions',
                'orderable' => false,
                'globalSearchable' => false,
                'className' => 'grid_row_actions',
                'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [
                            'confirmation' => [
                                'target' => '#modal-xl2',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-list',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['payer']
                            ],
                            'payer' => [
                                'target' => '#modal-lg',
                                'url' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ],
                            'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ]
                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('inscription/inscription/index_suivi_formation.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'id' => $id,
            'titre' => $titre,
        ]);
    }





    #[Route('/formation', name: 'app_inscription_inscription_formation_index', methods: ['GET', 'POST'])]
    public function indexFormation(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, SessionInterface $session): Response
    {

        //dd($etat);
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $anneeScolaire  = $session->get('annee_scolaire');

        //Code Etudiant, Filière, Nom et prénoms, Coût formation, Total payé, Solde
        $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
            ->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                return $preinscription->getEtudiant()->getNomComplet();
            }])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant'])
            ->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye'])
            ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                return   abs($preinscription->getMontant() - $preinscription->getTotalPaye());
            }])
            ->add('etat', TextColumn::class, ['label' => 'Etape', 'render' => function ($value, Inscription $preinscription) {
                return   $preinscription->getEtat();
            }]);
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $isEtudiant, $anneeScolaire) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c');
                /*   ->andWhere('p.etat = :etat')
                    ->setParameter('etat', $etat); */
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }

                if ($anneeScolaire) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_formation_');

        $renders = [
            /*  'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeance' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'), */
            'delete' => new ActionRender(function () {
                return false;
            }),
            'suivi' => new ActionRender(function () {
                return true;
            }),
            /*  'payer' => new ActionRender(fn () => $etat == 'valide' && $isEtudiant == false), */

        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions',
                'orderable' => false,
                'globalSearchable' => false,
                'className' => 'grid_row_actions',
                'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [

                            'suivi' => [
                                'url' => $this->generateUrl('app_home_timeline_etudiant_formation_index', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-folder',
                                'attrs' => ['class' => 'btn-primary'],
                                'render' => $renders['suivi']
                            ],
                            /*  'payer' => [
                                'target' => '#modal-lg',
                                'url' => $this->generateUrl('app_inscription_inscription_paiement', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ], */
                            /*  'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ], */
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ]
                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('inscription/inscription/index_formation.html.twig', [
            'datatable' => $table,
        ]);
    }


    #[Route('/{etat}', name: 'app_inscription_inscription_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserInterface $user, string $etat, DataTableFactory $dataTableFactory, EcheancierRepository $echeancierRepository, SessionInterface $session): Response
    {

        // dd($etat);
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $anneeScolaire = $session->get('anneeScolaire');

        $titre = '';
        if ($etat == "echeance_soumis") {
            $titre = "Liste des inscriptions | validation des échéances";
        } elseif ($etat == "valide") {
            $titre = "Liste des inscriptions en attente de paiement";
        } elseif ($etat == "rejete") {
            $titre = "Liste des inscriptions rejettées | en attente d'écheancier";
        } elseif ($etat == 'attente_echeance') {
            $titre = "Liste des inscriptions en attente d'écheancier";
        } elseif ($etat == 'valide_classe') {
            $titre = "Liste des étudiants en attente d'affectation dans une classe";
        } else {
            $titre = "Liste des inscriptions soldées";
        }
        //Code Etudiant, Filière, Nom et prénoms, Coût formation, Total payé, Solde


        if ($etat == 'valide') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
                ->add('classe', TextColumn::class, ['field' => 'classe.libelle', 'label' => 'Classe']);

            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }

            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return   abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }])
                ->add('nombre', NumberFormatColumn::class, ['label' => 'Nombre ACF', 'className' => 'text-center', 'render' => function ($value, Inscription $preinscription) {
                    return abs($preinscription->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                        return $ligne->getEtat() == 'attente_confirmation';
                    })->count());
                }]);
        } elseif ($etat == 'echeance_soumis') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);

            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }

            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            //$table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye']);
            $table
                ->add('nombre', NumberFormatColumn::class, ['label' => 'Nombre échéances', 'render' => function ($value, Inscription $preinscription) {
                    return   count($preinscription->getEcheanciers());
                }])
                /*     ->add('dateDebut', TextColumn::class, ['label' => 'Nombre échéances', 'render' => function ($value, Inscription $preinscription) use ($echeancierRepository) {
                    dd($echeancierRepository->findAllEcheanceDateFirst($preinscription->getId())[0]['debut']);
                    return;
                }]); */

                ->add('debut', DateTimeColumn::class, ['label' => 'Date début', 'format' => 'd-m-Y', 'searchable' => false, 'render' => function ($value, Inscription $preinscription) use ($echeancierRepository) {

                    return $echeancierRepository->findAllEcheanceDateFirst($preinscription->getId())[0]['debut'];
                }])
                ->add('debutFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y', 'searchable' => false, 'render' => function ($value, Inscription $preinscription) use ($echeancierRepository) {

                    return $echeancierRepository->findAllEcheanceDateFirst($preinscription->getId())[0]['fin'];
                }]);
        } elseif ($etat == 'attente_echeance') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);
            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
        } elseif ($etat == 'valide_classe') {
            $table = $dataTableFactory->create()
                ->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
                ->add('niveau', TextColumn::class, ['field' => 'niveau.code', 'label' => 'Code niveau'])
                ->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }])

                ->add('dateInscription', DateTimeColumn::class, ['label' => "Date de la demande d'inscription", 'format' => 'd-m-Y']);
        } else {

            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
                ->add('classe', TextColumn::class, ['field' => 'classe.libelle', 'label' => 'Classe']);

            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }

            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.montant']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return    abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }]);
            /* ->add('nombre', TextColumn::class, ['label' => 'Nombre attente confirmation', 'render' => function ($value, Inscription $preinscription) {
                    return abs($preinscription->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                        return $ligne->getEtat() == 'attente_confirmation';
                    })->count());
                }]); */
        }



        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat, $anneeScolaire) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant', 'classe'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.classe', 'classe')
                    ->leftJoin('p.caissiere', 'c');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
                if ($etat == 'attente_echeance') {
                    $qb->andWhere('p.etat = :etat')
                        ->orWhere('p.etat = :etat2')
                        ->setParameter('etat', 'attente_echeancier')
                        ->setParameter('etat2', 'rejete');
                } elseif ($etat == 'valide_classe') {
                    $qb->andWhere('p.etat = :etat')
                        ->andWhere('p.classe is null')
                        ->setParameter('etat', 'valide');
                } elseif ($etat == 'valide') {
                    $qb->andWhere('p.etat = :etat')
                        ->andWhere('p.classe IS NOT NULL')
                        ->setParameter('etat', $etat);
                } else {
                    $qb->andWhere('p.etat = :etat')
                        ->setParameter('etat', $etat);
                }

                if ($anneeScolaire) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_' . $etat);

        $renders = [
            'edit_etudiant' => new ActionRender(fn() => $etat == 'attente_echeance' || $etat == 'rejete'),
            'delete' => new ActionRender(fn() => $etat == 'valide_classe'),
            'edit' => new ActionRender(fn() => $etat == 'echeance_soumis'),
            'recu' => new ActionRender(function () use ($etat) {
                if ($etat == 'solde') {
                    return true;
                } else {
                    return false;
                }
            }),
            /* 'delete' => new ActionRender(function () {
                return true;
            }), */
            'confirmation' => new ActionRender(function () use ($etat) {
                return $etat == 'valide';
            }),
            'classe' => new ActionRender(function () use ($etat) {
                return $etat == 'valide_classe';
            }),
            'validation_classe' => new ActionRender(function () use ($etat) {
                return $etat == 'valide_classe';
            }),
            'payer' => new ActionRender(fn() => $etat == 'valide' && $isEtudiant == false),
            //'recu' => new ActionRender(fn () => $etat == 'solde'),

        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions',
                'orderable' => false,
                'globalSearchable' => false,
                'className' => 'grid_row_actions',
                'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [
                            'classe' => [
                                'target' => '#modal-xl',
                                'url' => $this->generateUrl('verification_validation_dossier_inscription', ['id' => $context->getEtudiant()->getId(), 'inscription' => $value]),

                                //'url' => $this->generateUrl('validation_echeancier_affectation_classe', ['id' => $value]),
                                //'url' => $this->generateUrl('app_inscription_affectation_classe', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                /*  'target' => '#exampleModalSizeLg1', */
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['classe']
                            ],
                            'validation_classe' => [
                                //hhdhhdhdhdhd  'site_information_validation_direct_after_demande_since_precription',{'id':etudiant.id,'preinscription':preinscription.id
                                'target' => '#modal-xl',
                                'url' => $this->generateUrl('site_information_validation_direct_after_demande_since_inscription', ['id' => $context->getEtudiant()->getId(), 'inscription' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-check-circle',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['validation_classe']
                            ],
                            'recu' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_etudiant',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack'],
                                'render' => $renders['recu']
                            ],
                            'imprime_recu_confirmation' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_attente_confirmation',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-primary btn-stack'],
                                'render' => new ActionRender(fn() => $context->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                                    return $ligne->getEtat() == 'attente_confirmation';
                                })->count() >= 1 && $context->getEtat() == 'valide')
                            ],
                            'imprime_recu_confirmation_ok' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_etudiant',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack'],
                                'render' => new ActionRender(fn() => $context->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                                    return $ligne->getEtat() == 'attente_confirmation';
                                })->count() == 0 && $context->getEtat() == 'valide')
                            ],

                            /*  'recu' => [
                                'target' => '#modal-lg',
                                'url' => $this->generateUrl('app_comptabilite_inscription_print', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => new ActionRender(fn() => !$context->getSortieCaisse() || $context->getSortieCaisse()->getEtat() == 'demande')
                            ], */
                            'confirmation' => [
                                'target' => '#modal-xl2',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-list',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['payer']
                            ],
                            'payer' => [
                                'target' => '#modal-xl2',
                                'url' => $this->generateUrl('app_config_paiement_inscription_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ],
                            'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],

                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ]
                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('inscription/inscription/index_first.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'titre' => $titre,
        ]);
    }


    #[Route(path: '/validation/echeancier/demande/{id}', name: 'validation_echeancier_affectation_classe', methods: ['GET', 'POST'])]
    public function informationAdminNewDirectAfterDemandeEtudiantSincePreinscription(
        Request $request,
        FormError $formError,
        Inscription $inscription,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        EcheancierRepository $echeancierRepository,
        EntityManagerInterface $entityManager,
        Service $service,
        SessionInterface $session,
        EcheancierNiveauRepository $echeancierNiveauRepository
    ): Response {


        /* foreach ($frais as $key => $value) {
            $sommeFrais += (int)$value->getMontant();
        } */
        $bloc_echeancier = new BlocEcheancier();

        /*  $bloc_echeancier->setClasse($classeRepository->find(21)); */
        $bloc_echeancier->setDateInscription(new DateTime());
        $bloc_echeancier->setTotal('0');


        $inscription->addBlocEcheancier($bloc_echeancier);

        foreach ($echeancierNiveauRepository->findBy(["niveau" => $inscriptionRepository->find($inscription)->getNiveau()]) as $key => $echeancierNiveau) {
            $echeancierProvisoire = new EcheancierProvisoire();
            $echeancierProvisoire->setDateVersement($echeancierNiveau->getDateVersement());
            $echeancierProvisoire->setNumero($echeancierNiveau->getNumero());
            $echeancierProvisoire->setMontant($echeancierNiveau->getMontant());

            $bloc_echeancier->addEcheancierProvisoire($echeancierProvisoire);
        }




        $validationGroups = ['Default', 'FileRequired', 'autre'];
        //dd($niveauRepository->findNiveauDisponible(21));

        $form = $this->createForm(InscriptionEcheancierNewType::class, $inscription, [
            'method' => 'POST',
            'anneeScolaire' => $session->get("anneeScolaire"),
            'niveau' => $inscription->getNiveau(),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('validation_echeancier_affectation_classe', [
                'id' =>  $inscription->getId(),


            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');


            $blocEcheanciers = $form->get('blocEcheanciers')->getData();

            //  dd($blocEcheanciers);


            if ($form->isValid()) {


                $responseRegister = $service->registerEcheancieOnlyInscription($blocEcheanciers, $inscription);

                if ($responseRegister) {
                    // $inscription->setClasse()
                    $inscriptionRepository->save($inscription, true);


                    $statut = 1;
                    $message       = 'Opération effectuée avec succès';
                    $this->addFlash('success', $message);
                } else {
                    $statut = 0;
                    $message       = "Opération échouée car le montant total à payer est different du montant total de l 'échanece";
                    $this->addFlash('danger', $message);
                }


                $data = true;
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/new_validation.html.twig', [
            'etudiant' => $inscription,
            'etat' => 'ok',
            'form' => $form->createView(),
            'frais' => 3,
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }

    #[Route('/{etat}', name: 'app_inscription_inscription_list_ls', methods: ['GET', 'POST'])]
    public function indexListe(Request $request, UserInterface $user, string $etat, DataTableFactory $dataTableFactory, SessionInterface $session): Response
    {
        //dd("");
        //dd($etat);
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $anneeScolaire = $session->get('anneeScolaire');

        if ($etat == 'valide_classe') {
            $table = $dataTableFactory->create()
                ->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
                ->add('niveau', TextColumn::class, ['field' => 'niveau.code', 'label' => 'Code niveau'])
                ->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }])

                ->add('dateInscription', DateTimeColumn::class, ['label' => "Date de la demande d'inscription", 'format' => 'd-m-Y']);
        } else {
            $table = $dataTableFactory->create()
                ->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
                ->add('niveau', TextColumn::class, ['field' => 'niveau.libelle', 'label' => 'Niveau'])
                ->add('dateInscription', DateTimeColumn::class, ['label' => 'Date création', 'format' => 'd-m-Y']);

            if ($etat != 'attente_echeancier') {
                $table->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']);
            }


            if (!$isEtudiant) {
                $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                    ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                    ->add('nom_prenom', TextColumn::class, ['label' => 'Demandeur', 'render' => function ($value, Inscription $preinscription) {
                        return $preinscription->getEtudiant()->getNomComplet();
                    }]);
            }
            if ($etat == 'valide') {
                $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant']);
                $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'info.datePaiement', 'format' => 'd-m-Y']);
            }
        }

        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat, $isEtudiant, $anneeScolaire) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant,res'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('niveau.responsable', 'res')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
                if ($etat == 'attente_echeance') {
                    $qb->orWhere('p.etat = :etat')
                        ->orWhere('p.etat = :etat2')
                        ->setParameter('etat', 'attente_echeancier')
                        ->setParameter('etat2', 'rejete');
                } else {
                    $qb->andWhere('p.etat = :etat')
                        ->setParameter('etat', $etat);
                }
                if ($user->getPersonne()->getFonction()->getCode() == 'DR') {
                    $qb->andWhere("res = :user")
                        ->setParameter('user', $user->getPersonne());
                }
                if ($anneeScolaire) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_list_' . $etat);

        $renders = [
            /* 'edit' =>  new ActionRender(function () {
                return true;
            }), */
            'edit_etudiant' => new ActionRender(fn() => $etat == 'attente_echeancier' || $etat == 'rejete'),
            'edit' => new ActionRender(fn() => $etat == 'echeance_soumis'),
            'payer' => new ActionRender(fn() => $etat == 'valide'),
            'delete' => new ActionRender(function () {
                return true;
            }),
            // 'recu' => new ActionRender(fn () => $etat == 'solde'),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions',
                'orderable' => false,
                'globalSearchable' => false,
                'className' => 'grid_row_actions',
                'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'recu' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack']
                                //, 'render' => new ActionRender(fn() => $source || $etat != 'cree')
                            ],
                            'payer' => [
                                'url' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ],

                            'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Editer l\'etudiant'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ]
                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('inscription/inscription/index.html.twig', [
            'datatable' => $table,
            'etat' => $etat
        ]);
    }


    #[Route('/new', name: 'app_inscription_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_inscription_index');




            if ($form->isValid()) {

                $entityManager->persist($inscription);
                $entityManager->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/affectation/classe/{id}', name: 'app_inscription_affectation_classe', methods: ['GET', 'POST'])]
    public function affectationClasse(Request $request, EntityManagerInterface $entityManager, FormError $formError, Inscription $inscription): Response
    {
        /*  $inscription = new Inscription(); */
        $form = $this->createForm(InscriptionAffectationClasseType::class, $inscription, [
            'method' => 'POST',
            'niveau' => $inscription->getNiveau(),
            'action' => $this->generateUrl('app_inscription_affectation_classe', [
                'id' =>  $inscription->getId()
            ])
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');




            if ($form->isValid()) {

                $entityManager->persist($inscription);
                $entityManager->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/affectation_classe.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_inscription_inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('inscription/inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }
    #[Route('/{id}/show/confirmation', name: 'app_inscription_inscription_paiement_confirmation', methods: ['GET'])]
    public function showConfirmation(Inscription $inscription): Response
    {
        return $this->render('inscription/inscription/show_confirmation.html.twig', [
            'infoinscription' => $inscription->getInfoInscriptions(),
        ]);
    }

    #[Route('/{id}/edit/admin', name: 'app_inscription_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository): Response
    {

        // dd('');

        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_edit', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            $ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            if ($form->isValid()) {
                $somme = 0;

                foreach ($ligne as $key => $value) {
                    $somme += $value->getMontant();
                }

                if ($inscription->getMontant() == $somme) {

                    if ($form->getClickedButton()->getName() === 'passer') {
                        $workflow_data->apply($inscription, 'valider');


                        $entityManager->persist($inscription);
                    } elseif ($form->getClickedButton()->getName() === 'rejeter') {
                        $workflow_data->apply($inscription, 'rejet');

                        $entityManager->persist($inscription);
                    }

                    $entityManager->persist($inscription);
                    $entityManager->flush();

                    $message       = sprintf('Opération effectuée avec succès');
                    $showAlert = false;
                    $statut = 1;
                    $this->addFlash('success', $message);
                } else {
                    $showAlert = true;
                    $statut = 0;

                    if ($inscription->getMontant() > $somme) {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est inferieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    } else {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est superieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    }

                    $this->addFlash('danger', $message);
                }


                $data = true;

                //$message       = 'Opération effectuée avec succès';

            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'showAlert'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'frais' => $fraisRepository->findBy(array('inscription' => $inscription->getId())),
        ]);
    }
    #[Route('/{id}/edit/admin/echeancier', name: 'app_inscription_inscription_edit_admin_echeancier', methods: ['GET', 'POST'])]
    public function editAdminEcheancier(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository, InfoInscriptionRepository $infoInscriptionRepository): Response
    {

        // dd('');

        $form = $this->createForm(InscriptionAdminType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_edit_admin_echeancier', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        //$classeOlde = $inscription->getClasse();

        //dd($classeOlde);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_etudiant_admin_index');

            $versements = $infoInscriptionRepository->findOneBy(['inscription' => $inscription->getId()]);
            $echeanciers = $form->get('echeanciers')->getData();
            $fraisInscription = $form->get('fraisInscriptions')->getData();
            $classe = $form->get('classe')->getData();
          
            $allInscriptions = $inscriptionRepository->findOneBy(['etudiant' => $inscription->getEtudiant(), 'classe' => $classe]);

            $somme = 0;
            $sommeFraisInscription = 0;
            foreach ($echeanciers as $key => $value) {
                $somme += (int)$value->getMontant();
            }
            foreach ($fraisInscription as $key => $frais) {
                $sommeFraisInscription += (int)$frais->getMontant();
            }

            //dd($somme);

            // dd($allInscriptions->getId(), $inscription->getId());
            if ($form->isValid()) {
                if ((int)$inscription->getMontant() == $somme) {

                    $inscription->setMontant($somme);
                    $inscription->setTotalPaye('0');

                    if($allInscriptions){
                        if (($allInscriptions->getId() == $inscription->getId()) || ($allInscriptions == null)) {
                            $entityManager->persist($inscription);
                            $entityManager->flush();
    
                            if ($versements) {
                                foreach ($versements as $key => $versement) {
                                    $entityManager->remove($versement);
                                    $entityManager->flush();
                                }
                            }
    
                            $statut = 1;
                            $message       = 'Opération effectuée avec succès';
                        } else {
                            $statut = 0;
                            $message       = 'Opération échouée car cet étudaiant à deja une inscription pour cette classe';
                        }
                    }else{
                        $inscription->setClasse($classe);
                        $entityManager->persist($inscription);
                        $entityManager->flush();

                        if ($versements) {
                            foreach ($versements as $key => $versement) {
                                $entityManager->remove($versement);
                                $entityManager->flush();
                            }
                        }

                        $statut = 1;
                        $message       = 'Opération effectuée avec succès';
/* 
                        $statut = 0;
                        $message       = 'Opération échouée car nous ne trouvons aucune inscription correspondant à cette classe pour ce etudiant'; */
                    }
                } else {

                    $statut = 0;
                    $message       = "Opération échouée car le montant total à payer est different du montant total de l 'échanece";
                    $this->addFlash('danger', $message);
                }

                $data = true;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/edit_echeancier.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),

        ]);
    }
    #[Route('/{id}/edit/rejeter', name: 'app_inscription_inscription_rejeter', methods: ['GET', 'POST'])]
    public function editRejeter(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository): Response
    {

        // dd('');

        $form = $this->createForm(InscriptionRejeterType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_rejeter', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            //$ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            if ($form->isValid()) {

                $inscription->setEtat('rejete');
                $entityManager->persist($inscription);
                $entityManager->flush();


                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $fullRedirect = true;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/rejeter.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/liste/versements', name: 'app_inscription_liste_versements', methods: ['GET', 'POST'])]
    public function listeVersment(InfoInscriptionRepository $infoInscriptionRepository, Inscription $inscription)
    {
        return $this->render('inscription/inscription/liste_versement.html.twig', [
            'inscription' => $inscription,
            'versements' => $infoInscriptionRepository->findBy(['inscription' => $inscription, 'etat' => 'payer']),
        ]);
    }

    const TAB_ID = 'parametre-tabs';

    #[Route('/{id}/paiement/admin/recapitulatif', name: 'app_inscription_inscription_paiement_recapitulatif', methods: ['GET', 'POST'])]
    public function paiementRecapitulatif(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, InfoInscriptionRepository $infoInscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository, EcheancierRepository $echeancierRepository, UserInterface $user, NaturePaiementRepository $naturePaiementRepository, Service $service): Response
    {


        return $this->render('inscription/inscription/edit_paiement_recapitulatif.html.twig', [
            'inscription' => $inscription,
            'echeanciers' => $echeancierRepository->findBy(array('inscription' => $inscription->getId())),
            'versements' => $infoInscriptionRepository->findBy(['inscription' => $inscription]),
        ]);
    }


    #[Route('/{id}/paiement/admin/ok', name: 'app_inscription_inscription_paiement_ok', methods: ['GET', 'POST'])]
    public function paiement(Request $request, FraisInscriptionRepository $fraisInscriptionRepository, TypeFraisRepository $typeFraisRepository, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, InfoInscriptionRepository $infoInscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository, EcheancierRepository $echeancierRepository, UserInterface $user, NaturePaiementRepository $naturePaiementRepository, Service $service): Response
    {



        $form = $this->createForm(InscriptionPayementType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_paiement_ok', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $url = null;
        $tabId = null;
        $statut = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_inscription_frais_index');
            //$ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            $echeanciers = $echeancierRepository->findAllEcheance($inscription->getId());
            $date = $form->get('datePaiement')->getData();
            $mode =  $naturePaiementRepository->find($form->get('modePaiement')->getData()->getId());
            $type =  $typeFraisRepository->find($form->get('typeFrais')->getData()->getId());

            $montant = (int) $form->get('montant')->getData();
            $typeFrais =  $form->get('typeFrais')->getData();

            $somme = 0;

            $fraisInscriptionData = $fraisInscriptionRepository->findOneBy(['inscription' => $inscription->getId(), 'typeFrais' => $typeFrais]);
            $infoInscriptiondata = $infoInscriptionRepository->findBy(['inscription' => $inscription->getId(), 'typeFrais' => $typeFrais]);

            if($infoInscriptiondata){

                foreach ($infoInscriptiondata as $key => $info) {
                $somme += (int)$info->getMontant();
                $resteAPayer = abs((int)$inscription->getMontant() - $somme);

            }
            }else{
                $resteAPayer = abs((int)$inscription->getMontant());
        
            }

            
    
            $allData = [

                'echeanciers' => $echeanciers,
                'date' => $date,
                'modePaiement' => $mode,
                'typeFrais' => $type,
                'montant' => $montant,
                'numeroCheque' => $form->get('numeroCheque')->getData(),
                'banque' => $form->get('banque')->getData(),
                'contact' => $form->get('contact')->getData(),
                'dateCheque' => $form->get('dateCheque')->getData()
            ];



            if ($form->isValid()) {

                if ($resteAPayer >= $montant) {
                    $service->paiementInscriptionNew($inscription, $allData);

                    $load_tab = true;
                    $statut = 1;

                    $message = sprintf('Opération effectuée avec succès');
                    $this->addFlash('success', $message);
                } else {

                    $statut = 0;

                    $message = sprintf('Désole échec de paiement car le montant  saisi est superieur au montant  [%s] qui reste à payer pour un montant total de  %s', $resteAPayer, $fraisInscriptionData->getMontant());
                    $this->addFlash('danger', $message);
                }



                $url = [
                    'url' => $this->generateUrl('app_inscription_inscription_paiement_recapitulatif', [
                        'id' => $inscription->getId()
                    ]),
                    'tab' => '#module0',
                    'current' => '#module0'
                ];

                $tabId = self::TAB_ID;
                $redirect = $url['url'];
                /*    if ($inscription->getMontant() == $inscription->getTotalPaye()) {
                    $statut = 1;

                    $this->addFlash('success', $message);
                } else {
                    $statut = 0;
                } */

                $data = true;
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {

                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/edit_paiement.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'echeanciers' => $echeancierRepository->findBy(array('inscription' => $inscription->getId())),
            'versements' => $infoInscriptionRepository->findBy(['inscription' => $inscription]),
        ]);
    }

    #[Route('/{id}/imprime/versement', name: 'app_comptabilite_print_inscription_versement', methods: ['GET'])]
    public function imprimerVersement($id, InfoInscription $versement, InfoPreinscriptionRepository $infoPreinscriptionRepository): Response
    {

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/recu_versement.html.twig", [
            'data' => $versement,
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'L',
            'protected' => true,

            'format' => 'A5',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' => $imgFiligrame,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    #[Route('/{id}/edit/etudiant', name: 'app_inscription_inscription_etudiant_edit', methods: ['GET', 'POST'])]
    public function editEtudiant(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, FormError $formError, FraisInscriptionRepository $fraisRepository): Response
    {

        // dd('');

        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_etudiant_edit', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            $ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            if ($form->isValid()) {
                $somme = 0;

                foreach ($ligne as $key => $value) {
                    $somme += $value->getMontant();
                }



                if ($inscription->getMontant() == $somme) {

                    if ($form->getClickedButton()->getName() === 'resoumettre') {
                        $workflow_data->apply($inscription, 'soumission');


                        $entityManager->persist($inscription);
                    } elseif ($form->getClickedButton()->getName() === 'retour') {
                        $workflow_data->apply($inscription, 'retour');

                        $entityManager->persist($inscription);
                    }

                    $entityManager->persist($inscription);
                    $entityManager->flush();

                    $message       = sprintf('Opération effectuée avec succès');
                    $showAlert = false;
                    $this->addFlash('success', $message);
                    $statut = 1;
                } else {
                    $showAlert = true;

                    if ($inscription->getMontant() > $somme) {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est inferieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    } else {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est superieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    }

                    $this->addFlash('danger', $message);
                    $statut = 0;
                }


                $data = true;

                //$message       = 'Opération effectuée avec succès';

            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'showAlert'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/edit_etudiant.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'frais' => $fraisRepository->findBy(array('inscription' => $inscription->getId())),
        ]);
    }

    #[Route('/def/{id}/delete', name: 'app_inscription_inscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Inscription $inscription, PreinscriptionRepository $preprescriptionRepository, InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager): Response
    {

        $count = count($preprescriptionRepository->findBy(['etudiant' => $inscription->getEtudiant(), 'etat' => 'valide'])) + count($inscriptionRepository->countInscriptionFordelete($inscription->getEtudiant()));

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_inscription_inscription_delete',
                    [
                        'id' => $inscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        $showAlert = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;



            $entityManager->remove($inscription);

            if (count($inscriptionRepository->findBy(['etudiant' => $inscription->getEtudiant()])) > 1) {
                $showAlert = true;
                $message = 'Nous avons supprimer cette inscription mais Implossibe de supprimer le compte car cet étudiant à d\'autres inscriptions';
            } else {

                /* $entityManager->remove($inscription->getEtudiant()); */
                if ($preprescriptionRepository->findBy(['etudiant' => $inscription->getEtudiant(), 'etat' => 'valide']) > 0) {
                    $showAlert = true;
                    $message = 'Nous avons supprimer cette inscription mais Implossibe de supprimer le compte car cet étudiant à des préinscriptions associées';
                } else {

                    $entityManager->remove($inscription->getEtudiant());
                    $message = 'Opération effectuée avec succès';
                }
            }

            $entityManager->flush();


            $redirect = $this->generateUrl('app_inscription_etudiant_admin_index');



            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data,
                'showAlert' => $showAlert
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->render('inscription/inscription/delete.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }
    #[Route('/retour/prinscription/{id}/delete', name: 'app_inscription_inscription_retour_preinscription_delete', methods: ['DELETE', 'GET'])]
    public function deleteRetourPreinscription(Request $request, Inscription $inscription, PreinscriptionRepository $preprescriptionRepository, InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager): Response
    {

        $count = count($preprescriptionRepository->findBy(['etudiant' => $inscription->getEtudiant(), 'etat' => 'valide'])) + count($inscriptionRepository->countInscriptionFordelete($inscription->getEtudiant()));

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_inscription_inscription_retour_preinscription_delete',
                    [
                        'id' => $inscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        $showAlert = false;
        if ($form->isSubmitted() && $form->isValid()) {

            $redirect = $this->generateUrl('app_home_timeline_index');

            $data = true;

            $entityManager->remove($inscription);
            $entityManager->flush();

            $entityManager->remove($inscription->getDeliberation());
            $entityManager->flush();

            $inscription->getDeliberation()->getPreinscription()->setEtat('valide');
            $inscription->getDeliberation()->getPreinscription()->setEtatDeliberation(etatDeliberation: 'pas_deliberer');
            $entityManager->persist($inscription->getDeliberation()->getPreinscription());
            $entityManager->flush();

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->render('inscription/inscription/delete.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }
}
