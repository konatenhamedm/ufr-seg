<?php

namespace App\Controller\Direction;

use App\Entity\Deliberation;
use App\Entity\DeliberationPreinscription;
use App\Entity\Echeancier;
use App\Entity\Examen;
use App\Entity\FraisInscription;
use App\Entity\Inscription;
use App\Entity\LigneDeliberation;
use App\Entity\Mention;
use App\Entity\Niveau;
use App\Entity\Preinscription;
use App\Form\DeliberationType;
use App\Repository\AnneeScolaireRepository;
use App\Repository\DeliberationPreinscriptionRepository;
use App\Repository\DeliberationRepository;
use App\Repository\ExamenRepository;
use App\Repository\FraisRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PreinscriptionRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\MapColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/admin/direction/deliberation')]
class DeliberationController extends AbstractController
{

    #[Route('/update/preinscription',  methods: ['GET'])]
    public function updatePreinscription(PreinscriptionRepository $preinscriptionRepository)
    {

        $pre = $preinscriptionRepository->findAll();
        foreach ($pre as $key => $value) {
            $value->setMontant($value->getNiveau()->getFiliere()->getMontantPreinscription());

            $preinscriptionRepository->add($value, true);
        }

        return $this->json([], 200);
    }


    #[Route('/', name: 'app_direction_deliberation_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {

        $annee = $session->get('anneeScolaire');


        if ($annee == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Examen::class,
            ])
            ->setName('dt_app_direction_examen_deliberation');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
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
                'render' => function ($value, Examen $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'deliberation' => [
                                'url' => $this->generateUrl('app_direction_deliberation_new', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
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


        return $this->render('direction/deliberation/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/timeline', name: 'app_direction_deliberation_time_index', methods: ['GET', 'POST'])]
    public function indexTimeLigne(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $anneeScolaire = $session->get("anneeScolaire");

        $annee = $session->get('anneeScolaire');


        if ($annee == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'n.code'])
            ->add('dateExamen', DateTimeColumn::class, ['label' => 'Date Prévue', 'format' => 'd-m-Y'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Examen::class,
                'query' => function (QueryBuilder $qb) use ($user, $anneeScolaire) {
                    $qb->select(['d', 'n', 'f', 'res'])
                        ->from(Examen::class, 'd')
                        ->innerJoin('d.niveau', 'n')
                        ->join('n.responsable', 'res')
                        ->innerJoin('n.filiere', 'f')
                        ->orderBy('d.id', 'DESC');

                    if ($user->getPersonne()->getFonction()->getCode() == 'DR') {
                        $qb->andWhere("res = :user")
                            ->setParameter('user', $user->getPersonne());
                    }

                    if ($anneeScolaire != null) {

                        $qb->andWhere('n.anneeScolaire = :anneeScolaire')
                            ->setParameter('anneeScolaire', $anneeScolaire);
                    }
                }
            ])
            ->setName('dt_app_direction_deliberation_time');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delib' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];
        //$gridId = $filiere;

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
                'render' => function ($value, Examen $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'target' => '#modal-lg225',
                                'url' => $this->generateUrl('app_direction_examen_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delib' => [
                                'url' => $this->generateUrl('app_direction_deliberation_historique', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-folder2-open',
                                'attrs' => ['class' => 'btn-primary'],
                                'render' => $renders['edit']
                            ],

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


        return $this->render('direction/deliberation/index_timeline.html.twig', [
            'datatable' => $table
        ]);
    }



    #[Route('/historique/{id}', name: 'app_direction_deliberation_historique', methods: ['GET', 'POST'])]
    public function historique(Request $request, UserInterface $user, $id, DataTableFactory $dataTableFactory, ExamenRepository $examenRepository, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $anneeScolaire = $session->get("anneeScolaire");

        //$annee = $session->get('anneeScolaire');


        if ($anneeScolaire == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }
        $table = $dataTableFactory->create()
            //->add('date', TextColumn::class, ['label' => 'Code'])
            ->add('candidat', TextColumn::class, ['label' => 'Candidat', 'render' => function ($value, Deliberation $deliberation) {
                return $deliberation->getPreinscription()->getNomComplet();
            }])
            ->add('examen', TextColumn::class, ['label' => 'Examen', 'field' => 'ex.libelle'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'n.libelle'])
            ->add('total', NumberFormatColumn::class, ['label' => 'Total'])
            ->add('moyenne', NumberFormatColumn::class, ['label' => 'Moyenne'])
            ->add('mention', TextColumn::class, ['label' => 'Mention', 'field' => 'm.libelle'])
            ->add('etat', TextColumn::class, ['label' => 'Decision', 'raw' => false,  'render' => function ($value, Deliberation $context) {
                if ($value == 'valide') {
                    return sprintf('<span class="badge badge-success">Admis(e)</span>');
                }
                return sprintf('<span class="badge badge-success">Ajourné(e)</span>');
            }])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Deliberation::class,
                'query' => function (QueryBuilder $qb) use ($user, $id, $anneeScolaire) {
                    $qb->select(['d', 'ex', 'n', 'm', 'dp', 'p', 'res'])
                        ->from(Deliberation::class, 'd')
                        ->join('d.infoPreinscription', 'dp')
                        ->join('dp.preinscription', 'p')
                        ->join('d.examen', 'ex')
                        ->join('ex.niveau', 'n')
                        ->join('n.responsable', 'res')
                        ->join('d.mention', 'm')
                        ->andWhere('ex.id = :id')
                        ->setParameter('id', $id);
                    /*  ->orderBy('d.date', 'DESC') */
                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->andWhere('p.etudiant = :etudiant')->setParameter('etudiant', $user->getPersonne());
                    }

                    if ($this->isGranted('ROLE_DIRECTEUR')) {
                        $qb->andWhere('res.id = :id')
                            ->setParameter('id', $user->getPersonne()->getId());
                    }
                    if ($anneeScolaire != null) {

                        $qb->andWhere('n.anneeScolaire = :anneeScolaire')
                            ->setParameter('anneeScolaire', $anneeScolaire);
                    }
                }
            ])
            ->setName('dt_app_direction_examen_deliberation_historique' . $id);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
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
                'render' => function ($value, Deliberation $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_direction_deliberation_edit', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_direction_deliberation_show', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_direction_deliberation_delete', ['id' => $value]),
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


        return $this->render('direction/deliberation/index_historique.html.twig', [
            'datatable' => $table,
            'title' => 'Historique des délibérations',
            'id' => $id,
            'examen' => $examenRepository->find($id)
        ]);
    }
    #[Route('/traitement/examen/', name: 'app_direction_deliberation_liste_etudiant_traitement_exament_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function ListeEtudiantTraitementExament(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, ExamenRepository $examenRepository, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $niveau = $request->query->get('niveau');

        $anneeScolaire = $session->get("anneeScolaire");

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_direction_deliberation_liste_etudiant_traitement_exament_index', [
                /* 'etat' => $etat */])
        ])->add('niveau', EntityType::class, [
            'class' => Niveau::class,
            'choice_label' => 'code',
            'label' => 'Niveau',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2'],
            'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                return $er->createQueryBuilder('c')
                    ->andWhere('c.anneeScolaire = :anneeScolaire')
                    ->setParameter('anneeScolaire', $anneeScolaire)
                    ->orderBy('c.id', 'DESC');
            },
        ]);


        if ($anneeScolaire == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }

        /*  if ($etat == 'delibere') {
            $titre = "Liste des délibérations";
            $table = $dataTableFactory->create()
                ->add('candidat', TextColumn::class, ['label' => 'Candidat', 'render' => function ($value, Deliberation $deliberation) {
                    return $deliberation->getPreinscription()->getNomComplet();
                }])
                ->add('examen', TextColumn::class, ['label' => 'Examen', 'field' => 'ex.libelle'])
                ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'n.code'])
                ->add('total', NumberFormatColumn::class, ['label' => 'Total'])
                ->add('moyenne', NumberFormatColumn::class, ['label' => 'Moyenne'])
                ->add('mention', TextColumn::class, ['label' => 'Mention', 'field' => 'm.libelle'])
                ->add('etat', TextColumn::class, ['label' => 'Decision', 'raw' => false,  'render' => function ($value, Deliberation $context) {
                    if ($value == 'valide') {
                        return sprintf('<span class="badge badge-success">Admis(e)</span>');
                    }
                    return sprintf('<span class="badge badge-success">Ajourné(e)</span>');
                }])

                ->createAdapter(ORMAdapter::class, [
                    'entity' => Deliberation::class,
                    'query' => function (QueryBuilder $qb) use ($user, $anneeScolaire, $niveau) {
                        $qb->select(['d', 'ex', 'n', 'm', 'dp', 'p', 'res'])
                            ->from(Deliberation::class, 'd')
                            ->join('d.infoPreinscription', 'dp')
                            ->join('dp.preinscription', 'p')
                            ->join('d.examen', 'ex')
                            ->join('ex.niveau', 'n')
                            ->join('n.responsable', 'res')
                            ->join('d.mention', 'm');

                        if ($this->isGranted('ROLE_ETUDIANT')) {
                            $qb->andWhere('p.etudiant = :etudiant')->setParameter('etudiant', $user->getPersonne());
                        }

                        if ($this->isGranted('ROLE_DIRECTEUR')) {
                            $qb->andWhere('res.id = :id')
                                ->setParameter('id', $user->getPersonne()->getId());
                        }
                        if ($anneeScolaire != null) {

                            $qb->andWhere('n.anneeScolaire = :anneeScolaire')
                                ->setParameter('anneeScolaire', $anneeScolaire);
                        }

                        //    if ($niveau) {

                        if ($niveau) {
                            $qb->andWhere('n.id = :niveau')
                                ->setParameter('niveau', $niveau);
                        }
                        //}
                    }
                ])
                ->setName('dt_app_direction_deliberation_liste_etudiant_traitement_exament_' . $etat . '_' . $niveau);
        } else { */
        $titre = "Liste des étudiants en attente de traitement";

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code Preinscription'])
            ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                return   $preinscription->getEtudiant()->getNomComplet();
            }])

            ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.code'])
            ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date pré-inscription', 'format' => 'd/m/Y', "searchable" => false,])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant'])
            /*   ->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']) */
            //->add('montantPreinscription', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Preinscription::class,
                'query' => function (QueryBuilder $qb) use ($user, $anneeScolaire, $niveau) {
                    $qb->select('e, filiere, etudiant,niveau,c')
                        ->from(Preinscription::class, 'e')
                        ->join('e.etudiant', 'etudiant')
                        ->join('e.niveau', 'niveau')
                        /* ->join('niveau', 'niveau') */
                        ->join('niveau.filiere', 'filiere')
                        ->leftJoin('e.caissiere', 'c')
                        ->andWhere('e.etat in (:statut)')
                        ->andWhere('e.etatDeliberation = :etatDeliberation')
                        ->setParameter('etatDeliberation', 'pas_deliberer')
                        ->setParameter('statut', ['valide']);

                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->andWhere('e.etudiant = :etudiant')
                            ->setParameter('etudiant', $user->getPersonne());
                    }
                    if ($anneeScolaire != null) {

                        $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                            ->setParameter('anneeScolaire', $anneeScolaire);
                    }
                    if ($niveau) {
                        $qb->andWhere('niveau.id = :niveau')
                            ->setParameter('niveau', $niveau);
                    }
                }
            ])
            ->setName('dt_app_direction_deliberation_liste_etudiant_traitement_exament_' . $niveau);
        /*  } */



        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];
        $gridId =  $niveau;

        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            /*     if ($etat == 'delibere') {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions',
                    'orderable' => false,
                    'globalSearchable' => false,
                    'className' => 'grid_row_actions',
                    'render' => function ($value, Deliberation $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                            'target' => '#modal-lg',

                            'actions' => [
                              
                                'show' => [
                                    'url' => $this->generateUrl('app_direction_deliberation_show', ['id' => $value]),
                                    'ajax' => true,
                                    'stacked' => false,
                                    'icon' => '%icon% bi bi-eye',
                                    'attrs' => ['class' => 'btn-main'],
                                    'render' => $renders['edit']
                                ],
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            } else {
            } */
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('direction/deliberation/index_traitement_examen.html.twig', [
            'datatable' => $table,
            'title' => $titre,
            'etat' => "kj",
            'form' => $builder->getForm(),
            'grid_id' => $gridId
            //'examen' => $examenRepository->find($id)
        ]);
    }
    #[Route('/traitement/examen/after/deliberation', name: 'app_direction_deliberation_liste_etudiant_traitement_exament_after_deliberation_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function ListeEtudiantTraitementExamentAfterDeliberation(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, ExamenRepository $examenRepository, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $niveau = $request->query->get('niveau');

        $anneeScolaire = $session->get("anneeScolaire");

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_direction_deliberation_liste_etudiant_traitement_exament_after_deliberation_index', [
                /* 'etat' => $etat */])
        ])->add('niveau', EntityType::class, [
            'class' => Niveau::class,
            'choice_label' => 'code',
            'label' => 'Niveau',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2'],
            'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                return $er->createQueryBuilder('c')
                    ->andWhere('c.anneeScolaire = :anneeScolaire')
                    ->setParameter('anneeScolaire', $anneeScolaire)
                    ->orderBy('c.id', 'DESC');
            },
        ]);


        if ($anneeScolaire == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }


        $titre = "Liste des délibérations";
        $table = $dataTableFactory->create()
            ->add('candidat', TextColumn::class, ['label' => 'Candidat', 'render' => function ($value, Deliberation $deliberation) {
                return $deliberation->getPreinscription()->getNomComplet();
            }])
            ->add('examen', TextColumn::class, ['label' => 'Examen', 'field' => 'ex.libelle'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'n.code'])
            ->add('total', NumberFormatColumn::class, ['label' => 'Total'])
            ->add('moyenne', NumberFormatColumn::class, ['label' => 'Moyenne'])
            ->add('mention', TextColumn::class, ['label' => 'Mention', 'field' => 'm.libelle'])
            ->add('etat', TextColumn::class, ['label' => 'Decision', 'raw' => false,  'render' => function ($value, Deliberation $context) {
                if ($value == 'valide') {
                    return sprintf('<span class="badge badge-success">Admis(e)</span>');
                }
                return sprintf('<span class="badge badge-success">Ajourné(e)</span>');
            }])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Deliberation::class,
                'query' => function (QueryBuilder $qb) use ($user, $anneeScolaire, $niveau) {
                    $qb->select(['d', 'ex', 'n', 'm', 'dp', 'p', 'res'])
                        ->from(Deliberation::class, 'd')
                        ->join('d.infoPreinscription', 'dp')
                        ->join('dp.preinscription', 'p')
                        ->join('d.examen', 'ex')
                        ->join('ex.niveau', 'n')
                        ->join('n.responsable', 'res')
                        ->join('d.mention', 'm');

                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->andWhere('p.etudiant = :etudiant')->setParameter('etudiant', $user->getPersonne());
                    }

                    if ($this->isGranted('ROLE_DIRECTEUR')) {
                        $qb->andWhere('res.id = :id')
                            ->setParameter('id', $user->getPersonne()->getId());
                    }
                    if ($anneeScolaire != null) {

                        $qb->andWhere('n.anneeScolaire = :anneeScolaire')
                            ->setParameter('anneeScolaire', $anneeScolaire);
                    }

                    //    if ($niveau) {

                    if ($niveau) {
                        $qb->andWhere('n.id = :niveau')
                            ->setParameter('niveau', $niveau);
                    }
                    //}
                }
            ])
            ->setName('dt_app_direction_deliberation_liste_etudiant_traitement_exament_after_deliberation_'  . $niveau);


        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];
        $gridId =  $niveau;

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
                'render' => function ($value, Deliberation $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [

                            'show' => [
                                'url' => $this->generateUrl('app_direction_deliberation_show', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
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


        return $this->render('direction/deliberation/index_traitement_examen_after_deliberation.html.twig', [
            'datatable' => $table,
            'title' => $titre,
            'etat' => "kj",
            'form' => $builder->getForm(),
            'grid_id' => $gridId
            //'examen' => $examenRepository->find($id)
        ]);
    }


    #[Route('/{id}/new', name: 'app_direction_deliberation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Examen $examen, EntityManagerInterface $entityManager, FormError $formError, FraisRepository $fraisRepository, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $deliberation = new Deliberation();
        $deliberation->setExamen($examen);
        $mentions = $entityManager->getRepository(Mention::class)->findAll();
        $etudiant = $request->query->get('etudiant');

        $annee = $session->get('anneeScolaire');


        if ($annee == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }


        foreach ($examen->getMatiereExamens() as $matiereExamen) {
            $ligne = new LigneDeliberation();
            $ligne->setMatiereExamen($matiereExamen);
            $ligne->setCoefficient($matiereExamen->getCoefficient());
            $deliberation->addLigneDeliberation($ligne);
        }

        $results = [];
        foreach ($mentions as $mention) {
            $results["{$mention->getMoyenneMin()}-{$mention->getMoyenneMax()}"] = $mention->getLibelle();
        }
        $form = $this->createForm(DeliberationType::class, $deliberation, [
            'method' => 'POST',
            'examen' => $examen,
            'type' => 'new',
            'action' => $this->generateUrl('app_direction_deliberation_new', ['id' => $examen->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;
        $fullRedirect = false;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_direction_deliberation_new', ['id' => $examen->getId()]);

            //dd($form->get('infoPreinscription'));


            if ($form->isValid()) {
                $deliberation->updateTotal();
                $moyenne = $deliberation->getMoyenne();

                $preinscription = $deliberation->getPreinscription();

                $etat = $deliberation->getEtat();

                if ($etat == 'valide') {
                    $niveau = $examen->getNiveau();
                    $preinscription->setEtatDeliberation('deliberer');

                    $somme = 0;

                    /*      $allFrais = $fraisRepository->findBy(array('niveau' => $niveau));
                    foreach ($allFrais as $key => $value) {
                       
                    }
 */
                    $inscription = new Inscription();
                    $inscription->setEtudiant($preinscription->getEtudiant());
                    $inscription->setNiveau($examen->getNiveau());
                    $inscription->setDeliberation($deliberation);
                    //$inscription->setMontant($preinscription->getCode());
                    $inscription->setEtat('valide');
                    $inscription->setCode($preinscription->getCode());
                    $inscription->setCodeUtilisateur($this->getUser()->getEmail());
                    foreach ($niveau->getFrais() as $frais) {
                        $fraisInscription = new FraisInscription();
                        $fraisInscription->setTypeFrais($frais->getTypeFrais());
                        $fraisInscription->setMontant($frais->getMontant());
                        $inscription->addFraisInscription($fraisInscription);
                        $somme += $frais->getMontant();
                    }
                    $inscription->setMontant($somme);


                    $entityManager->persist($inscription);

                    /* foreach ($examen->getNiveau()->getEcheancierNiveaux() as $key => $echeancierNiveau) {
                        $echenacierInscription = new Echeancier();
                        $echenacierInscription->setDateCreation(new DateTime());
                        $echenacierInscription->setMontant($echeancierNiveau->getMontant());
                        $echenacierInscription->setEtat("pas_payer");
                        $echenacierInscription->setTotaPayer(0);
                        $echenacierInscription->setInscription($inscription);
                        $entityManager->persist($echenacierInscription);
                        $entityManager->flush();
                    } */
                } elseif ($etat == 'rejet') {
                    $preinscription->setEtat('ajourne_inscription');
                    $preinscription->setEtatDeliberation('deliberer');
                }

                foreach ($mentions as $mention) {
                    if ($moyenne >= $mention->getMoyenneMin() && $mention->getMoyenneMax() >= $moyenne) {
                        $deliberation->setMention($mention);
                        break;
                    }
                }


                $entityManager->persist($deliberation);
                $entityManager->flush();

                $fullRedirect = true;

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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('direction/deliberation/new.html.twig', [
            'deliberation' => $deliberation,
            'examen' => $examen,
            'mentions' => json_encode($results),
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/new/historique', name: 'app_direction_deliberation_historique_new', methods: ['GET', 'POST'])]
    public function newHistorique(Request $request, Examen $examen, EntityManagerInterface $entityManager, FormError $formError, FraisRepository $fraisRepository, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $deliberation = new Deliberation();
        $deliberation->setExamen($examen);
        $mentions = $entityManager->getRepository(Mention::class)->findAll();
        $etudiant = $request->query->get('etudiant');

        $annee = $session->get('anneeScolaire');


        if ($annee == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }



        foreach ($examen->getMatiereExamens() as $matiereExamen) {
            $ligne = new LigneDeliberation();
            $ligne->setMatiereExamen($matiereExamen);
            $ligne->setCoefficient($matiereExamen->getCoefficient());
            $deliberation->addLigneDeliberation($ligne);
        }

        $results = [];
        foreach ($mentions as $mention) {
            $results["{$mention->getMoyenneMin()}-{$mention->getMoyenneMax()}"] = $mention->getLibelle();
        }
        $form = $this->createForm(DeliberationType::class, $deliberation, [
            'method' => 'POST',
            'examen' => $examen,
            'type' => 'historique',
            'action' => $this->generateUrl('app_direction_deliberation_historique_new', ['id' => $examen->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;
        $fullRedirect = false;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_direction_deliberation_historique', ['id' => $examen->getId()]);

            //dd($form->get('infoPreinscription'));


            if ($form->isValid()) {
                $deliberation->updateTotal();
                $moyenne = $deliberation->getMoyenne();

                $preinscription = $deliberation->getPreinscription();

                $etat = $deliberation->getEtat();

                if ($etat == 'valide') {
                    $niveau = $examen->getNiveau();
                    $preinscription->setEtatDeliberation('deliberer');

                    $somme = 0;

                    /*      $allFrais = $fraisRepository->findBy(array('niveau' => $niveau));
                    foreach ($allFrais as $key => $value) {
                       
                    }
 */
                    $inscription = new Inscription();
                    $inscription->setEtudiant($preinscription->getEtudiant());
                    $inscription->setNiveau($examen->getNiveau());
                    //$inscription->setMontant($preinscription->getCode());
                    $inscription->setEtat('attente_echeancier');
                    $inscription->setCode($preinscription->getCode());
                    $inscription->setCodeUtilisateur($this->getUser()->getEmail());
                    foreach ($niveau->getFrais() as $frais) {
                        $fraisInscription = new FraisInscription();
                        $fraisInscription->setTypeFrais($frais->getTypeFrais());
                        $fraisInscription->setMontant($frais->getMontant());
                        $inscription->addFraisInscription($fraisInscription);
                        $somme += $frais->getMontant();
                    }
                    $inscription->setMontant($somme);


                    $entityManager->persist($inscription);
                } elseif ($etat == 'rejet') {
                    $preinscription->setEtat('ajourne_inscription');
                    $preinscription->setEtatDeliberation('deliberer');
                }

                foreach ($mentions as $mention) {
                    if ($moyenne >= $mention->getMoyenneMin() && $mention->getMoyenneMax() >= $moyenne) {
                        $deliberation->setMention($mention);
                        break;
                    }
                }


                $entityManager->persist($deliberation);
                $entityManager->flush();

                $fullRedirect = true;

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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('direction/deliberation/historique_new.html.twig', [
            'deliberation' => $deliberation,
            'examen' => $examen,
            'mentions' => json_encode($results),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_direction_deliberation_show', methods: ['GET'])]
    public function show(Deliberation $deliberation): Response
    {

        return $this->render('direction/deliberation/show.html.twig', [
            'deliberation' => $deliberation,
            'examen' => $deliberation->getExamen()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_direction_deliberation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deliberation $deliberation, DeliberationPreinscriptionRepository $deliberationPreinscriptionRepository, PreinscriptionRepository $preinscriptionRepository, InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager, FormError $formError, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {

        // $anneeScolaire = $session->get("anneeScolaire");

        $annee = $session->get('anneeScolaire');


        if ($annee == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }
        $examen = $deliberation->getExamen();

        $mentions = $entityManager->getRepository(Mention::class)->findAll();
        $results = [];
        foreach ($mentions as $mention) {
            $results["{$mention->getMoyenneMin()}-{$mention->getMoyenneMax()}"] = $mention->getLibelle();
        }

        $form = $this->createForm(DeliberationType::class, $deliberation, [
            'method' => 'POST',
            'examen' => $examen,
            'type' => 'edit',
            'action' => $this->generateUrl('app_direction_deliberation_edit', [
                'id' =>  $deliberation->getId()
            ])
        ]);

        $inscription = $inscriptionRepository->findOneBy(['deliberation' => $deliberation]);
        $preinscription = $deliberationPreinscriptionRepository->findOneBy(['deliberation' => $deliberation])->getPreinscription();
        //dd($preinscription);

        $form->remove('etudiant');
        $form->remove('dateExamen');

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            // $redirect = $this->generateUrl('app_direction_deliberation_historique');
            $redirect = $this->generateUrl('app_direction_deliberation_new', ['id' => $examen->getId()]);



            if ($form->isValid()) {

                $deliberation->updateTotal();
                $moyenne = $deliberation->getMoyenne();

                foreach ($mentions as $mention) {
                    if ($moyenne >= $mention->getMoyenneMin() && $mention->getMoyenneMax() >= $moyenne) {
                        $deliberation->setMention($mention);
                        break;
                    }
                }

                $etat = $deliberation->getEtat();

                if ($inscription->getClasse() == null) {
                    if ($etat != 'valide') {


                        if ($inscription->getClasse() == null) {

                            $inscription->setEtat('examen_echoue');
                            $entityManager->persist($inscription);

                            $preinscription->setEtat('ajourne_inscription');
                            $entityManager->persist($preinscription);

                            $entityManager->flush();
                        }
                    }
                    $entityManager->persist($deliberation);
                    $entityManager->flush();
                    $message       = 'Opération effectuée avec succès';
                    $this->addFlash('success', $message);
                    $statut = 1;
                } else {
                    $statut = 0;
                    $message       = 'Oups mais vous pouvez plus de modification dans cette deliberation ';
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

        return $this->render('direction/deliberation/edit.html.twig', [
            'deliberation' => $deliberation,
            'examen' => $examen,
            'mentions' => json_encode($results),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_direction_deliberation_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Deliberation $deliberation, InscriptionRepository $inscriptionRepository, DeliberationPreinscriptionRepository $deliberationPreinscriptionRepository, EntityManagerInterface $entityManager): Response
    {
        $inscription = $inscriptionRepository->findOneBy(['deliberation' => $deliberation]);
        $preinscription = $deliberationPreinscriptionRepository->findOneBy(['deliberation' => $deliberation])->getPreinscription();

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_direction_deliberation_delete',
                    [
                        'id' => $deliberation->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $redirect = $this->generateUrl('app_direction_deliberation_new', ['id' => $deliberation->getExamen()->getId()]);

            $data = true;
            if ($inscription->getClasse() == null) {
                $entityManager->remove($deliberation);
                $entityManager->flush();

                $preinscription->setEtat('attente_validation');
                $entityManager->persist($preinscription);
                $entityManager->flush();

                $message = 'Opération effectuée avec succès';

                $response = [
                    'statut'   => 1,
                    'message'  => $message,
                    'redirect' => $redirect,
                    'data' => $data
                ];
            } else {
                $message = 'Oups mais vous pouvez plus de modification pour cette deliberation ';

                $response = [
                    'statut'   => 0,
                    'message'  => $message,
                    'redirect' => $redirect,
                    'data' => $data
                ];
            }






            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->render('direction/deliberation/delete.html.twig', [
            'deliberation' => $deliberation,
            'form' => $form,
        ]);
    }
}
