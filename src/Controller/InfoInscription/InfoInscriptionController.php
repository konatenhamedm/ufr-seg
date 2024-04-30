<?php

namespace App\Controller\InfoInscription;

use App\Controller\FileTrait;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\Niveau;
use App\Entity\Utilisateur;
use App\Form\InfoInscriptionType;
use App\Form\InfoInscriptionVersementAdminType;
use App\Form\InfoInscriptionVersementType;
use App\Repository\EcheancierRepository;
use App\Repository\InfoInscriptionRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NiveauRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
use App\Service\Service;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/admin/infoinscription/info/inscription')]
class InfoInscriptionController extends AbstractController
{
    use FileTrait;

    #[Route('liste/versement/{id}/', name: 'app_inscription_liste_versement_index', methods: ['GET', 'POST'])]
    public function indexListeVersement(Request $request, DataTableFactory $dataTableFactory, $id): Response
    {
        $table = $dataTableFactory->create()
            ->add('mode', TextColumn::class, ['label' => 'Mode Paiement', 'field' => 'mode.libelle'])
            ->add('typeFrais', TextColumn::class, ['label' => 'Type Frais', 'field' => 'type.libelle'])
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd-m-Y', 'searchable' => false])
            ->add('montant', TextColumn::class, ['label' => 'Montant',])
            ->createAdapter(ORMAdapter::class, [
                'entity' => InfoInscription::class,
                'query' => function (QueryBuilder $qb) use ($id) {
                    $qb->select('u, mode,inscription,type')
                        ->from(InfoInscription::class, 'u')
                        ->join('u.modePaiement', 'mode')
                        ->join('u.typeFrais', 'type')
                        ->join('u.inscription', 'inscription')
                        ->andWhere('u.etat = :etat')
                        ->andWhere('inscription.id = :id')
                        ->setParameter('id', $id)
                        ->setParameter('etat', 'payer');
                }
            ])
            ->setName('dt_app_inscription_liste_versement' . $id);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'first_print' =>  new ActionRender(function () {
                return true;
            }),
            'second_print' =>  new ActionRender(function () {
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

        /*    <a title="Modification" href="{{ path('app_infoinscription_info_inscription_edit',{'id':ligne.id }) }}" class="btn btn-primary btn-sm test" data-bs-stacked-toggle="modal" data-bs-stacked-modal="#modal-xl2"><i class="bi bi-pen text-light"></i></a>
        <a title="" href="{{ path('default_print_iframe',{'r':'app_comptabilite_print_inscription_versement','params': {'id': ligne.id}}) }}" class="btn btn-warning btn-sm test" data-bs-stacked-toggle="modal" data-bs-stacked-modal="#modal-xl2"><i class="bi bi-printer text-light"></i></a>
         <a title="" href="{{ path('app_infoinscription_info_inscription_delete',{'id':ligne.id})}}" class="btn btn-danger btn-sm test" data-bs-stacked-toggle="modal" data-bs-stacked-modal="#exampleModalSizeLg1"> <i class="bi bi-trash text-light"></i></a>
         <a title="" href="{{ path('default_print_iframe',{'r':'app_comptabilite_inscription_print','params': {'id': ligne.inscription.id}}) }}" class="btn btn-success btn-sm test" data-bs-stacked-toggle="modal" data-bs-stacked-modal="#modal-xl2"><i class="bi bi-printer text-light"></i></a>
            */

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, InfoInscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'first_print' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_print_inscription_versement',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-warning '],
                                'render' => $renders['first_print']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ],
                            'second_print' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main '],
                                'render' => $renders['second_print']
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


        return $this->render('infoinscription/info_inscription/liste_versement.html.twig', [
            'datatable' => $table,
            'id' => $id
        ]);
    }




    #[Route('/imprime/all/{etat}', name: 'app_comptabilite_print_all_point_cheque', methods: ['GET', 'POST'])]
    public function imprimerAll(Request $request, $etat, InfoInscriptionRepository $infoInscription, InscriptionRepository $inscriptionRepository, NiveauRepository $niveauRepository): Response
    {



        $preinscriptions = $infoInscription->getData($etat);


        //dd($preinscriptions);

        //$data = $preinscriptions;


        // }
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("infoinscription/info_inscription/imprime.html.twig", [
            'data' => $preinscriptions,

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
            'watermarkImg' => $imgFiligrame,
            'entreprise' => ''
        ], true);
    }

    #[Route('/imprime/all2/{id}', name: 'app_comptabilite_print_all_point_cheque2', methods: ['GET', 'POST'])]
    public function imprimerAll2(Request $request, $id, InfoInscriptionRepository $infoInscription, InscriptionRepository $inscriptionRepository, NiveauRepository $niveauRepository): Response
    {

        //dd($id);


        $preinscriptions = $infoInscription->getData();


        //dd($preinscriptions);

        //$data = $preinscriptions;


        // }
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("infoinscription/info_inscription/imprime.html.twig", [
            'data' => $preinscriptions,

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
            'watermarkImg' => $imgFiligrame,
            'entreprise' => ''
        ], true);
    }

    #[Route('/point/cheque/', name: 'app_infoinscription_info_inscription_point_cheque_index',  methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexPointCheque(Request $request, DataTableFactory $dataTableFactory, UserInterface $user,): Response
    {
        //dd('juj')
        $niveau = $request->query->get('niveau');
        $caissiere = $request->query->get('caissiere');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $mode = $request->query->get('mode');
        //dd($niveau, $dateDebut);

        /* return  $this->redirectToRoute($this->generateUrl('app_comptabilite_print_all_point_cheque2', [
            'id' => 3
        ])); */

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_infoinscription_info_inscription_point_cheque_index', compact('niveau', 'caissiere', 'dateDebut', 'dateFin', 'mode'))
        ])->add('niveau', EntityType::class, [
            'class' => Niveau::class,
            'choice_label' => 'libelle',
            'label' => 'Niveau',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ])

            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('caissiere', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'getNomComplet',
                'label' => 'Caissière',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->join('c.personne', 'p')
                        ->join('p.fonction', 'f')
                        ->andWhere('f.code = :caissiere')
                        ->setParameter('caissiere', 'CAI')
                        ->orderBy('c.id', 'DESC');
                },
                'placeholder' => '---',
                'choice_attr' => function (Utilisateur $user) {
                    return ['data-type' => $user->getId()];
                },
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ]);


        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code étudiant'])
            ->add('nom', TextColumn::class, ['label' => 'Nom et prénoms', 'field' => 'et.getNomComplet'])
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd-m-Y', 'searchable' => false])
            ->add('mode', TextColumn::class, ['label' => 'Mode de paiment', 'field' => 'mode.libelle'])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant'])

            ->add('etat', TextColumn::class, ['label' => 'Etat', 'className' => ' w-50px', 'render' => function ($value, InfoInscription $infoInscription) {

                if ($infoInscription->getEtat() == 'attente_confirmation') {

                    return   '<span class="badge bg-danger">Attente confirmation</span>';
                } else {

                    return   '<span class="badge bg-danger">Attente traitement</span>';
                }
            }])
            ->add('caissiere', TextColumn::class, ['label' => 'Caissière', 'field' => 'c.getNomComplet'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => InfoInscription::class,
                'query' => function (QueryBuilder $qb) use ($niveau, $caissiere, $dateDebut, $dateFin, $user) {
                    $qb->select('e, mode, c,i,et,niveau,res')
                        ->from(InfoInscription::class, 'e')
                        ->join('e.modePaiement', 'mode')
                        ->join('e.inscription', 'i')
                        ->join('i.niveau', 'niveau')
                        ->join('niveau.responsable', 'res')
                        ->join('i.etudiant', 'et')
                        ->join('e.caissiere', 'c')
                        ->andWhere('mode.code = :code')
                        ->andWhere('e.etat = :eta')
                        ->setParameter('eta', 'attente_confirmation')
                        ->setParameter('code', 'CHQ');

                    if ($this->isGranted('ROLE_DIRECTEUR')) {
                        $qb->andWhere('res.id = :id')
                            ->setParameter('id', $user->getPersonne()->getId());
                    }

                    if ($niveau || $caissiere || $dateDebut || $dateFin) {
                        if ($niveau) {
                            $qb->andWhere('niveau.id = :niveau')
                                ->setParameter('niveau', $niveau);
                        }

                        if ($caissiere) {
                            $qb->andWhere('c.id = :caissiere')
                                ->setParameter('caissiere', $caissiere);
                        }

                        if ($dateDebut && $dateFin == null) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('e.datePaiement = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin && $dateDebut == null) {

                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('e.datePaiement  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {
                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('e.datePaiement BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }
                    }
                }
            ])
            ->setName('dt_app_infoinscription_info_inscription_point_cheque_' . $niveau . '_' . $caissiere);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'show' =>  new ActionRender(function () {
                return true;
            }),
            'imprime' =>  new ActionRender(function () {
                return false;
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
        ];

        $gridId = $niveau . '_' . $caissiere;
        // dd($gridId);

        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, InfoInscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [

                            /*  'imprime' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_print',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack']
                                //, 'render' => new ActionRender(fn() => $source || $etat != 'cree')
                            ], */
                            /*    'imprime_recu_confirmation' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_attente_confirmation',
                                    'params' => [
                                        'id' => $context->getInscription()->getId(),
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-primary btn-stack'],

                            ], */
                            'show' => [
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-check',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['show']
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


        return $this->render('infoinscription/info_inscription/index_point.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm(),
            'grid_id' => $gridId
        ]);
    }
    #[Route('/point/cheque/confirme', name: 'app_infoinscription_info_inscription_point_cheque_confirme_index',  methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexPointChequeConfirme(Request $request, DataTableFactory $dataTableFactory, UserInterface $user,): Response
    {
        //dd('juj')
        $niveau = $request->query->get('niveau');
        $caissiere = $request->query->get('caissiere');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $mode = $request->query->get('mode');
        //dd($niveau, $dateDebut);

        /* return  $this->redirectToRoute($this->generateUrl('app_comptabilite_print_all_point_cheque2', [
            'id' => 3
        ])); */

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_infoinscription_info_inscription_point_cheque_confirme_index', compact('niveau', 'caissiere', 'dateDebut', 'dateFin', 'mode'))
        ])->add('niveau', EntityType::class, [
            'class' => Niveau::class,
            'choice_label' => 'libelle',
            'label' => 'Niveau',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ])

            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('caissiere', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'getNomComplet',
                'label' => 'Caissière',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->join('c.personne', 'p')
                        ->join('p.fonction', 'f')
                        ->andWhere('f.code = :caissiere')
                        ->setParameter('caissiere', 'CAI')
                        ->orderBy('c.id', 'DESC');
                },
                'placeholder' => '---',
                'choice_attr' => function (Utilisateur $user) {
                    return ['data-type' => $user->getId()];
                },
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ]);


        $table = $dataTableFactory->create()
            ->add('nom', TextColumn::class, ['label' => 'Nom et prénoms', 'field' => 'et.getNomComplet'])
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd-m-Y', 'searchable' => false])
            ->add('mode', TextColumn::class, ['label' => 'Mode de paiment', 'field' => 'mode.libelle'])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant'])

            ->add('etat', TextColumn::class, ['label' => 'Etat', 'className' => ' w-50px', 'render' => function ($value, InfoInscription $infoInscription) {

                if ($infoInscription->getEtat() == 'attente_confirmation') {

                    return   '<span class="badge bg-danger">Attente confirmation</span>';
                } else {

                    return   '<span class="badge bg-danger">Attente traitement</span>';
                }
            }])
            ->add('caissiere', TextColumn::class, ['label' => 'Caissière', 'field' => 'c.getNomComplet'])
            ->add('dateValidation', DateTimeColumn::class, ['label' => 'Date de validation', 'format' => 'd-m-Y', 'searchable' => false])
            ->createAdapter(ORMAdapter::class, [
                'entity' => InfoInscription::class,
                'query' => function (QueryBuilder $qb) use ($niveau, $caissiere, $dateDebut, $dateFin, $user) {
                    $qb->select('e, mode, c,i,et,niveau,res')
                        ->from(InfoInscription::class, 'e')
                        ->join('e.modePaiement', 'mode')
                        ->join('e.inscription', 'i')
                        ->join('i.niveau', 'niveau')
                        ->join('niveau.responsable', 'res')
                        ->join('i.etudiant', 'et')
                        ->join('e.caissiere', 'c')
                        ->andWhere('mode.code = :code')
                        ->andWhere('e.etat = :eta')
                        ->setParameter('eta', 'payer')
                        ->setParameter('code', 'CHQ');

                    if ($this->isGranted('ROLE_DIRECTEUR')) {
                        $qb->andWhere('res.id = :id')
                            ->setParameter('id', $user->getPersonne()->getId());
                    }
                    if ($niveau || $caissiere || $dateDebut || $dateFin) {
                        if ($niveau) {
                            $qb->andWhere('niveau.id = :niveau')
                                ->setParameter('niveau', $niveau);
                        }

                        if ($caissiere) {
                            $qb->andWhere('c.id = :caissiere')
                                ->setParameter('caissiere', $caissiere);
                        }

                        if ($dateDebut && $dateFin == null) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('e.datePaiement = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin && $dateDebut == null) {

                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('e.datePaiement  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {
                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('e.datePaiement BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }
                    }
                }
            ])
            ->setName('dt_app_infoinscription_info_inscription_point_cheque_confirme_' . $niveau . '_' . $caissiere);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'imprime' =>  new ActionRender(function () {
                return false;
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
        ];

        $gridId = $niveau . '_' . $caissiere;
        // dd($gridId);

        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => []

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('infoinscription/info_inscription/index_point_cheque_confirme.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm(),
            'grid_id' => $gridId
        ]);
    }


    #[Route('/{id}', name: 'app_infoinscription_info_inscription_index', methods: ['GET', 'POST'])]
    public function index(Request $request, string $id, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd-m-Y', 'searchable' => false])
            ->add('mode', TextColumn::class, ['label' => 'Mode de paiment', 'field' => 'mode.libelle'])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant'])
            ->add('caissiere', TextColumn::class, ['label' => 'Caissière', 'field' => 'c.username'])
            ->add('etat', TextColumn::class, ['label' => 'Etat', 'render' => function ($value, InfoInscription $infoInscription) {

                if ($infoInscription->getEtat() == 'attente_confirmation') {

                    return   '<span class="badge bg-danger">Attente confirmation</span>';
                } else {

                    return   '<span class="badge bg-danger">Attente traitement</span>';
                }
            }])

            ->createAdapter(ORMAdapter::class, [
                'entity' => InfoInscription::class,
                'query' => function (QueryBuilder $qb) use ($id) {
                    $qb->select('e, mode, c,i')
                        ->from(InfoInscription::class, 'e')
                        ->join('e.modePaiement', 'mode')
                        ->join('e.inscription', 'i')
                        ->join('e.caissiere', 'c')
                        ->andWhere('i.id = :id')
                        ->andWhere('e.etat = :etat')
                        ->orWhere('e.etat = :etat2')
                        ->setParameter('etat', 'attente_confirmation')
                        ->setParameter('etat2', 'attente_traitement')
                        ->setParameter('id', $id);
                }
            ])
            ->setName('dt_app_infoinscription_info_inscription' . $id);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return false;
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, InfoInscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',
                        // data-bs-stacked-toggle="modal" data-bs-stacked-modal="#exampleModalSizeLg1"
                        'actions' => [
                            'edit' => [
                                //'target' => '#modal-small',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_delete', ['id' => $value]),
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


        return $this->render('infoinscription/info_inscription/index.html.twig', [
            'datatable' => $table,
            'id' => $id
        ]);
    }

    #[Route('/{id}/info/paiement', name: 'app_infoinscription_info_inscription_info_paiement_index', methods: ['GET', 'POST'])]
    public function indexInfoPaiement(Request $request, string $id, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('code', NumberFormatColumn::class, ['label' => 'Code'])
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd-m-Y', 'searchable' => false])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant'])
            ->add('mode', TextColumn::class, ['label' => 'Mode de paiment', 'field' => 'mode.libelle'])
            ->add('caissiere', TextColumn::class, ['label' => 'Caissière', 'field' => 'c.username'])
            /*  ->add('etat', TextColumn::class, ['label' => 'Etat', 'className' => 'w-200', 'render' => function ($value, InfoInscription $infoInscription) {

                if ($infoInscription->getEtat() == 'attente_confirmation') {

                    return   '<span class="badge bg-danger">Attente confirmation</span>';
                } else {

                    return   '<span class="badge bg-danger">Attente traitement</span>';
                }
            }]) */
            ->createAdapter(ORMAdapter::class, [
                'entity' => InfoInscription::class,
                'query' => function (QueryBuilder $qb) use ($id) {
                    $qb->select('e, mode, c,i')
                        ->from(InfoInscription::class, 'e')
                        ->join('e.modePaiement', 'mode')
                        ->join('e.inscription', 'i')
                        ->join('e.caissiere', 'c')
                        ->andWhere('i.id = :id')
                        ->setParameter('id', $id);
                }
            ])
            ->setName('dt_app_infoinscription_info_inscription_info_paiement' . $id);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'delete' => new ActionRender(function () {
                return false;
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, InfoInscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',
                        // data-bs-stacked-toggle="modal" data-bs-stacked-modal="#exampleModalSizeLg1"
                        'actions' => [
                            'edit' => [
                                //'target' => '#modal-small',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_delete', ['id' => $value]),
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


        return $this->render('infoinscription/info_inscription/index_info_paiement.html.twig', [
            'datatable' => $table,
            'id' => $id
        ]);
    }


    #[Route('/new', name: 'app_infoinscription_info_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $infoInscription = new InfoInscription();
        $form = $this->createForm(InfoInscriptionType::class, $infoInscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_infoinscription_info_inscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_infoinscription_info_inscription_index');




            if ($form->isValid()) {

                $entityManager->persist($infoInscription);
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

        return $this->render('infoinscription/info_inscription/new.html.twig', [
            'info_inscription' => $infoInscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_infoinscription_info_inscription_show', methods: ['GET'])]
    public function show(InfoInscription $infoInscription): Response
    {
        return $this->render('infoinscription/info_inscription/show.html.twig', [
            'info_inscription' => $infoInscription,
        ]);
    }
    const TAB_ID = 'parametre-tabs';
    #[Route('/{id}/edit', name: 'app_infoinscription_info_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        InfoInscription $infoInscription,
        EntityManagerInterface $entityManager,
        FormError $formError,
        InscriptionRepository $inscriptionRepository,
        EcheancierRepository $echeancierRepository,
        Service $service
    ): Response {

        $form = $this->createForm(InfoInscriptionVersementAdminType::class, $infoInscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_infoinscription_info_inscription_edit', [
                'id' =>  $infoInscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        $oldMontant = (int)$infoInscription->getMontant();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            /*  $redirect = $this->generateUrl('app_infoinscription_info_inscription_index', [
                'id' => $infoInscription->getInscription()->getId()
            ]); */
            /*  $redirect = $this->generateUrl('app_inscription_liste_versement_index', [
                'id' => $infoInscription->getInscription()->getId()
            ]); */

            $new_montant = (int)$form->get('montant')->getData();
            $inscription = $infoInscription->getInscription();


            if ($form->isValid()) {



                $entityManager->persist($infoInscription);
                $entityManager->flush();

                if ($oldMontant != $new_montant) {
                    $service->paiementInscriptionEdit($inscription);
                }

                $url = [
                    'url' => $this->generateUrl('app_inscription_liste_versement_index', [
                        'id' => $infoInscription->getInscription()->getId()
                    ]),
                    'tab' => '#module1',
                    'current' => '#module1'
                ];

                $tabId = self::TAB_ID;
                $redirect = $url['url'];

                $data = true;
                $load_tab = true;
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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('infoinscription/info_inscription/edit.html.twig', [
            'info_inscription' => $infoInscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_infoinscription_info_inscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Service $service, InfoInscription $infoInscription, InscriptionRepository $inscriptionRepository, EcheancierRepository $echeancierRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_infoinscription_info_inscription_delete',
                    [
                        'id' => $infoInscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $inscription = $infoInscription->getInscription();
            $data = true;
            $entityManager->remove($infoInscription);
            $entityManager->flush();

            $service->paiementInscriptionEdit($inscription);



            /*   $redirect = $this->generateUrl('app_inscription_liste_versement_index', [
                'id' => $infoInscription->getInscription()->getId()
            ]);
 */
            $message = 'Opération effectuée avec succès';

            $url = [
                'url' => $this->generateUrl('app_inscription_liste_versement_index', [
                    'id' => $infoInscription->getInscription()->getId()
                ]),
                'tab' => '#module1',
                'current' => '#module1'
            ];

            $tabId = self::TAB_ID;
            $redirect = $url['url'];


            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data,
                'url' => $url,
                'tabId' => $tabId
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->render('infoinscription/info_inscription/delete.html.twig', [
            'info_inscription' => $infoInscription,
            'form' => $form,
        ]);
    }
}
