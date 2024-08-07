<?php

namespace App\Controller\Comptabilite;

use App\Attribute\Search;
use App\Entity\Inscription;
use App\Entity\Preinscription;
use App\Form\InscriptionType;
use App\Repository\InfoPreinscriptionRepository;
use App\Repository\InscriptionRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Mpdf\MpdfException;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\FileTrait;
use App\Entity\Classe;
use App\Entity\Filiere;
use App\Entity\InfoInscription;
use App\Entity\InfoPreinscription;
use App\Entity\NaturePaiement;
use App\Entity\Niveau;
use App\Entity\TypeFrais;
use App\Entity\Utilisateur;
use App\Form\SearchType;
use App\Repository\InfoInscriptionRepository;
use App\Repository\NiveauRepository;
use App\Repository\PreinscriptionRepository;
use App\Service\Omines\Column\NumberFormatColumn;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/admin/comptabilite/inscription')]
class InscriptionController extends AbstractController
{
    use FileTrait;

    #[Route('/', name: 'app_comptabilite_inscription_index',  methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory, UserInterface $user): Response
    {
        //  $isDirecteur = $this->isGranted('ROLE_DIRECTEUR');

        $niveau = $request->query->get('niveau');
        $caissiere = $request->query->get('caissiere');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $mode = $request->query->get('mode');
        //dd($niveau, $dateDebut);

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_comptabilite_inscription_index', compact('niveau', 'caissiere', 'dateDebut', 'dateFin', 'mode'))
        ])->add('niveau', EntityType::class, [
            'class' => Niveau::class,
            'choice_label' => 'libelle',
            'label' => 'Niveau',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ])
            ->add('mode', EntityType::class, [
                'class' => NaturePaiement::class,
                'choice_label' => 'libelle',
                'label' => 'Mode de paiement',
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
            ->add('codePreinscription', TextColumn::class, ['label' => 'Code Préinscription', 'field' => 'p.getCode'])
            // ->add('datePreinscription', DateTimeColumn::class, [
            //     'label' => 'Date inscription',
            //     'format' => 'd-m-Y'
            // ])
            ->add('nom', TextColumn::class, ['label' => 'Nom et prénoms', 'field' => 'etudiant.getNomComplet'])
            ->add('sigleNiveauFiliere', TextColumn::class, ['label' => 'Sigle', 'field' => 'niveau.getSigle'])
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date paiement', 'format' => 'd-m-Y', 'field' => 'info.datePaiement'])
            ->add('montantPaiement', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant'])
            /*  ->add('montant', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant']) */
            ->add('mode', TextColumn::class, ['label' => 'Mode de paiement', 'render' => function ($value, InfoPreinscription $context) {
                return $context->getModePaiement() ? $context->getModePaiement()->getLibelle() : 'En attente de paiement';
            }])
            ->add('caissiere', TextColumn::class, ['label' => 'Caissière', 'field' => 'ca.getNomComplet'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => InfoPreinscription::class,
                'query' => function (QueryBuilder $qb) use ($user, $niveau, $caissiere, $dateDebut, $dateFin, $mode) {
                    $qb->select(['info'])
                        ->from(InfoPreinscription::class, 'info')
                        ->leftJoin('info.preinscription', 'p')
                        ->join('p.promotion', 'promotion')
                        ->join('promotion.niveau', 'niveau')
                        ->leftJoin('p.caissiere', 'ca')
                        ->join('niveau.filiere', 'filiere')
                        ->join('promotion.responsable', 'res')
                        ->join('p.etudiant', 'etudiant')
                        ->leftJoin('info.modePaiement', 'mode')
                        ->andWhere('p.etat = :etat')
                        ->setParameter('etat', 'valide')
                        ->orderBy('p.datePreinscription', 'DESC');

                    if ($niveau || $caissiere || $dateDebut || $dateFin || $mode) {
                        if ($niveau) {
                            $qb->andWhere('niveau.id = :niveau')
                                ->setParameter('niveau', $niveau);
                        }
                        if ($mode) {
                            $qb->andWhere('mode.id = :mode')
                                ->setParameter('mode', $mode);
                        }
                        if ($caissiere) {
                            $qb->andWhere('ca.id = :caissiere')
                                ->setParameter('caissiere', $caissiere);
                        }

                        if ($dateDebut && $dateFin == null) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('info.datePaiement = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin && $dateDebut == null) {

                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('info.datePaiement  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {

                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];
                            // dd($new_date_debut, $new_date_fin);

                            $qb->andWhere('info.datePaiement BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }
                    }


                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->orWhere('p.etudiant = :etudiant')
                            ->setParameter('etudiant', $user->getPersonne());
                    }


                    if ($user->getPersonne()->getFonction()->getCode() == 'DR') {
                        $qb->andWhere("res = :user")
                            ->setParameter('user', $user->getPersonne());
                    }
                }
            ])
            ->setName('dt_app_comptabilite_inscription_' . $niveau . '_' . $caissiere . '_' . $mode);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'imprime' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];

        $gridId = $niveau . '_' . $caissiere . '_' . $mode;
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, InfoPreinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_versement_index', ['id' => $context->getPreinscription()->getid()]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Frais \'écolage'],
                                'render' => $renders['edit']
                            ],
                            'imprime' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_print_preinscription',
                                    'params' => [
                                        'id' => $context->getPreinscription()->getid(),
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack']
                                //, 'render' => new ActionRender(fn() => $source || $etat != 'cree')
                            ],
                            /*  'imprime' => [
                                'url' => $this->generateUrl('app_comptabilite_print', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% fa fa-print',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Frais \'écolage'],
                                'render' => $renders['imprime']
                            ], */

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


        return $this->render('comptabilite/inscription/index.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm(),
            'grid_id' => $gridId
        ]);
    }
    #[Route('/paiement/scolarite', name: 'app_comptabilite_paiement_scolarite_index',  methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexPaiementScolarite(Request $request, DataTableFactory $dataTableFactory, UserInterface $user): Response
    {
        //  $isDirecteur = $this->isGranted('ROLE_DIRECTEUR');

        $niveau = $request->query->get('niveau');
        $caissiere = $request->query->get('caissiere');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $filiere = $request->query->get('filiere');
        $mode = $request->query->get('mode');
        $classe = $request->query->get('classe');
        $typeFrais = $request->query->get('typeFrais');

        $search = new Search();

        $builder = $this->createForm(SearchType::class, $search, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_comptabilite_paiement_scolarite_index', compact('niveau', 'caissiere', 'dateDebut', 'dateFin', 'mode', 'filiere', 'classe', 'typeFrais')),

        ]);

        $builder->handleRequest($request);


        if ($builder->isSubmitted()) {


            if ($builder->get('imprime')->isClicked()) {
                $redirect = $this->generateUrl('imprime_retour_achat_points', [
                    'niveau' => $builder->get('niveau')->getData()->getId()
                ]);


                return $this->redirect($redirect);
            }
        }

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code', 'field' => 'i.code'])
            ->add('nom', TextColumn::class, ['label' => 'Nom et prénoms', 'field' => 'etudiant.getNomComplet'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.getSigle'])
            ->add('classe', TextColumn::class, ['label' => 'Classe', 'field' => 'classe.libelle'])
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date paiement', 'format' => 'd-m-Y', 'field' => 'i.datePaiement'])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'i.montant'])
            ->add('caissiere', TextColumn::class, ['label' => 'Caissière', 'field' => 'ca.getNomComplet'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => InfoInscription::class,
                'query' => function (QueryBuilder $qb) use ($user, $niveau, $caissiere, $dateDebut, $dateFin, $mode, $filiere, $classe, $typeFrais) {
                    $qb->select(['i'])
                        ->from(InfoInscription::class, 'i')
                        ->join('i.inscription', 'p')
                        ->innerJoin('i.modePaiement', 'mode')
                        ->join('p.promotion', 'promotion')
                        ->join('promotion.niveau', 'niveau')
                        ->join('i.typeFrais', 'typeFrais')
                        ->join('p.classe', 'classe')
                        ->leftJoin('i.caissiere', 'ca')
                        ->join('niveau.filiere', 'filiere')
                        ->join('promotion.responsable', 'res')
                        ->join('p.etudiant', 'etudiant')
                        ->orderBy('i.datePaiement', 'DESC');

                    if ($niveau || $caissiere || $dateDebut || $dateFin || $mode || $filiere || $classe || $typeFrais) {
                        if ($filiere) {
                            $qb->andWhere('filiere.id = :filiere')
                                ->setParameter('filiere', $filiere);
                        }
                        if ($classe) {
                            $qb->andWhere('classe.id = :classe')
                                ->setParameter('classe', $classe);
                        }
                        if ($typeFrais) {
                            $qb->andWhere('typeFrais.id = :typeFrais')
                                ->setParameter('typeFrais', $typeFrais);
                        }
                        if ($niveau) {
                            $qb->andWhere('niveau.id = :niveau')
                                ->setParameter('niveau', $niveau);
                        }
                        if ($mode) {
                            $qb->andWhere('mode.id = :mode')
                                ->setParameter('mode', $mode);
                        }
                        if ($caissiere) {
                            $qb->andWhere('ca.id = :caissiere')
                                ->setParameter('caissiere', $caissiere);
                        }

                        if ($dateDebut && $dateFin == null) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('i.datePaiement = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin && $dateDebut == null) {

                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('i.datePaiement  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {

                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('i.datePaiement BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }

                        if ($user->getPersonne()->getFonction()->getCode() == 'DR') {
                            $qb->andWhere("res = :user")
                                ->setParameter('user', $user->getPersonne());
                        }
                    }
                }
            ])
            ->setName('dt_app_comptabilite_paiement_scolarite_' . $niveau . '_' . $caissiere . '_' . $mode . '_' . $classe . '_' . $typeFrais . '_' . $filiere);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'imprime' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];

        $gridId = $niveau . '_' . $caissiere . '_' . $mode . '_' . $classe . '_' . $typeFrais . '_' . $filiere;
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
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_versement_index', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Frais \'écolage'],
                                'render' => $renders['edit']
                            ],
                            'imprime' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_print_inscription_versement',
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
                            /*  'imprime' => [
                                'url' => $this->generateUrl('app_comptabilite_print', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% fa fa-print',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Frais \'écolage'],
                                'render' => $renders['imprime']
                            ], */

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


        return $this->render('comptabilite/inscription/index_scolarite_point.html.twig', [
            'datatable' => $table,
            'form' => $builder->createView(),
            'grid_id' => $gridId
        ]);
    }

    /**
     * @throws MpdfException
     */
    #[Route('/{id}/imprime', name: 'app_comptabilite_print', methods: ['GET'])]
    public function imprimer($id, Preinscription $preinscription, InfoPreinscriptionRepository $infoPreinscriptionRepository): Response
    {

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/recu.html.twig", [
            'data' => $preinscription,
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
    /**
     * @throws MpdfException
     */
    #[Route('/{id}/imprime/preinscription', name: 'app_comptabilite_print_preinscription', methods: ['GET'])]
    public function imprimerPreinscription($id, Preinscription $preinscription, InfoPreinscriptionRepository $infoPreinscriptionRepository): Response
    {

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/recu_preinscription.html.twig", [
            'data' => $preinscription,
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

    /**
     * @throws MpdfException
     */
    #[Route('/imprime/all/{niveau}/{caissiere}/{dateDebut}/{dateFin}/{mode}/point des versements', name: 'app_comptabilite_print_all', methods: ['GET', 'POST'])]
    public function imprimerAll(Request $request, $niveau, $caissiere, $dateDebut, $dateFin, $mode, InfoPreinscriptionRepository $infoPreinscriptionRepository, NiveauRepository $niveauRepository, PreinscriptionRepository $preinscriptionRepository): Response
    {

        // $niveau = $request->query->get('niveau');


        $totalImpaye = 0;
        $totalPayer = 0;


        // dd($_SESSION['token']);

        //$id = intval($request->query->get('niveau'));
        //$dateNiveau = $niveauRepository->find(intval($niveau))->getLibelle();
        /* if ($niveau) {
             = $niveauRepository->find(intval($niveau));
        } else {
            $dateNiveau = null;
        } */


        $preinscriptions = $infoPreinscriptionRepository->getListeRecouvrementParEtudiant($niveau);

        foreach ($preinscriptions as $key => $value) {

            if ($value['etat'] == "valide") {
                $totalPayer += $value['montant_preinscription'];
            } else {
                $totalImpaye += $value['montant_preinscription'];
            }
        }

        //dd($dateNiveau);
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/liste.html.twig", [
            'total_payer' => $totalPayer,
            'data' => $infoPreinscriptionRepository->searchResult($niveau, $caissiere, $dateDebut, $dateFin, $mode),
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'p',
            'protected' => true,
            'file_name' => "point_versments",

            'format' => 'A4',

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
    #[Route('/tester/imprime/uuu/{niveau}/{caissiere}/{dateDebut}/{dateFin}/{mode}/{classe}/{typeFrais}/{filiere}/point des versements', name: 'imprime_retour_achat_points', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function imprimerkk(Request $request, $niveau, $caissiere, $dateDebut, $dateFin, $mode, $classe, $typeFrais, $filiere, InfoInscriptionRepository $infoInscriptionRepository, NiveauRepository $niveauRepository, InscriptionRepository $inscriptionRepository): Response
    {

        $totalImpaye = 0;
        $totalPayer = 0;



        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/liste_versement.html.twig", [
            'total_payer' => $totalPayer,
            'data' => $infoInscriptionRepository->searchResult($niveau, $caissiere, $dateDebut, $dateFin, $mode, $classe, $typeFrais, $filiere),
            'total_impaye' => $totalImpaye,
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'p',
            'protected' => true,
            'file_name' => 'point_versements',

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' => $imgFiligrame,
            'entreprise' => ''
        ], true);
        /* $html = $this->renderView("site/tester.html.twig", [
            'niveau' => $niveau,
            'filiere' => $niveau,

        ]);

        $mpdf = new \Mpdf\Mpdf([

            'mode' => 'utf-8', 'format' => 'A5'
        ]);
        $mpdf->PageNumSubstitutions[] = [
            'from' => 1,
            'reset' => 0,
            'type' => 'I',
            'suppress' => 'on'
        ];

        $mpdf->WriteHTML($html);
        $mpdf->SetFontSize(6);
        $mpdf->Output();
        exit; */
    }
    #[Route('/imprime/versement/inscription/all', name: 'app_comptabilite_print_versement_inscription_all', methods: ['GET', 'POST'])]
    public function pointVersementInscription(Request $request, InfoInscriptionRepository $infoInscriptionRepository, NiveauRepository $niveauRepository, InscriptionRepository $inscriptionRepository): Response
    {




        $totalImpaye = 0;
        $totalPayer = 0;



        /*   foreach ($preinscriptions as $key => $value) {

            if ($value['etat'] == "valide") {
                $totalPayer += $value['montant_preinscription'];
            } else {
                $totalImpaye += $value['montant_preinscription'];
            }
        } */

        //dd($dateNiveau);
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/liste_versement.html.twig", [
            'total_payer' => $totalPayer,
            'data' => $infoInscriptionRepository->findAll(),
            'total_impaye' => $totalImpaye,
            'niveau' => 'eau'
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
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    /**
     * @throws MpdfException
     */
    #[Route('/imprime/etat/versement/all/1', name: 'app_affectation_materiel_location_sortie_index', methods: ['GET', 'POST'], options: ['expose' => true], condition: "request.query.has('filters')")]
    #[Route('/imprime/etat/versement/all', name: 'app_comptabilite_print_etat_versement_alll', methods: ['GET', 'POST'])]
    public function imprimerEtatVersement(Request $request, InfoPreinscriptionRepository $infoPreinscriptionRepository, InfoInscriptionRepository $infoInscriptionRepository, PreinscriptionRepository $preinscriptionRepository): Response
    {

        // $niveau = $request->query->get('niveau');
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        //dd($all['filters']);
        $dataAnnees = $infoInscriptionRepository->rangeDate();
        $annees = range($dataAnnees['min_year'], $dataAnnees['max_year']);

        //dd($infoInscriptionRepository->rangeSommeParAnnneNiveauEtudiant(32, 2023, 1));

        $dataArray = [];
        $dataArrayListe = [];

        foreach ($infoInscriptionRepository->getListeRecouvrement() as $key => $value) {


            foreach ($infoInscriptionRepository->getListeVersement() as $key => $liste) {

                if ($value['_etudiant_id'] == $liste['_etudiant_id'] &&  $value['niveau'] == $liste['niveau'])
                    $dataArrayListe[] = [
                        'montant' => $liste['somme'],
                        'year' => $liste['year'],

                    ];
            }

            $dataArray[] = [
                'nom_prenom' => $value['nom'] . ' ' . $value['prenom'],
                'montant' => $value['montant'],
                'etudiant' => $value['_etudiant_id'],
                'niveau' => $value['niveau'],
                'versements' => $dataArrayListe,

            ];
        }

        //dd($dataArray);



        //dd($dateNiveau);
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/etat_versement.html.twig", [

            'datas' => $dataArray,
            'dates' => $annees,
            'titre' => $dataAnnees
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
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    #[Route('/new', name: 'app_comptabilite_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_inscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_inscription_index');




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

        return $this->render('comptabilite/inscription/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilite_inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('comptabilite/inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilite_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_inscription_edit', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_inscription_index');




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

        return $this->render('comptabilite/inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_inscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_inscription_delete',
                    [
                        'id' => $inscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($inscription);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_comptabilite_inscription_index');

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

        return $this->render('comptabilite/inscription/delete.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }
}
