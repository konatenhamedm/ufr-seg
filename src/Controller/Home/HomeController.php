<?php

namespace App\Controller\Home;

use App\Controller\FileTrait;
use App\Entity\AnneeScolaire;
use App\Entity\Decision;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Entity\Preinscription;
use App\Form\EtudiantType;
use App\Form\EtudiantVerificationType;
use App\Form\PreinscriptionValidationType;
use App\Repository\AnneeScolaireRepository;
use App\Repository\DecisionRepository;
use App\Repository\EtudiantRepository;
use App\Repository\NiveauRepository;
use App\Repository\PersonneRepository;
use App\Repository\PreinscriptionRepository;
use App\Repository\UtilisateurRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Adapter\ORMAdapter;
use App\Service\Omines\Column\NumberFormatColumn;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Registry;

class HomeController extends AbstractController
{
    use FileTrait;


    #[Route(path: '/update-annee-scolaire/{id}', name: 'update_annee_scolaire', methods: ['POST'])]
    public function updateAnneeScolaire(Request $request, SessionInterface $session, $id, AnneeScolaireRepository $anneeScolaireRepository)
    {
        $annee = $session->get('anneeScolaire');



        if (!empty($annee)) {
            //unset($annee);
            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }
        // Supprimer l'ancienne valeur de la session (si nécessaire)
        // $session->remove('anneeScolaire');

        // Récupérer la nouvelle année scolaire par son ID
        $anneeScolaire = $anneeScolaireRepository->find($id);

        // Mettre à jour la session avec la nouvelle valeur
        $session->set('anneeScolaire', $anneeScolaire);

        return $this->json(['status' => 'success']);
    }

    protected const UPLOAD_PATH = 'media_etudiant';
    #[Route('/workflow/timeline', name: 'app_home_timeline_index')]
    public function index(UserInterface $user, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $annee = $session->get('anneeScolaire');


        if ($annee == null) {
            // dd($anneeScolaireRepository->findOneBy(['actif' => 1]));
            //unset($annee);
            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }

        //dd($annee);
        $modules = [
            [
                'label' => 'Etude de dossier',
                'icon' => 'bi bi-list',
                'module' => 'etude',
                'id' => 'module_etude',
                'href' => $this->generateUrl('app_config_preinscription_etude_dossier_index')
            ],
            [
                'label' => 'Paiement de préinscriptions',
                'icon' => 'bi bi-list',
                'module' => 'paiement',
                'id' => 'module_paiement',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_index')
            ],
            [
                'label' => 'Traitement après examen',
                'icon' => 'bi bi-list',
                'module' => 'traitement',
                'id' => 'module_traitement',
                'href' => $this->generateUrl('app_direction_deliberation_time_index')
            ],
            /* [
                'label' => 'Echéancier en attente de validation',
                'icon' => 'bi bi-list',
                'module' => 'echeancier',
                'id' => 'module_echeancier',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'echeance_soumis'])
            ], */
            [

                'label' => 'Affectation classe',
                'icon' => 'bi bi-list',
                'module' => 'classe',
                'id' => 'module_classe',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'valide_classe'])
            ],
            [
                'label' => 'Liste étudiants inscrits',
                'icon' => 'bi bi-list',
                'module' => 'inscrits',
                'id' => 'module_inscrits',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'valide'])
            ],
            [
                'label' => 'Dossiers soldés',
                'icon' => 'bi bi-list',
                'module' => 'dossier',
                'id' => 'module_dossier',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'solde'])
            ]
        ];

        return $this->render('home/config/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    #[Route('/workflow/etudiant', name: 'app_home_timeline_etudiant_index')]
    public function indexEtudiant(): Response
    {

        $modules = [
            /* [
                'label' => 'Etude de dossier',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'attente_validation'])
            ], */
            [
                'label' => 'En attente de paiement préinscription',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_config_preinscription_index')
                //'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_index')
            ],
            [
                'label' => 'Traitement après examen',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_deliberation_preinscription_index')
            ],
            /* [
                'label' => 'En attente échéancier de paiement',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'attente_echeance'])
            ],
            [
                'label' => 'En attente validation échéancier de paiement',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'echeance_soumis'])
            ],
 */
            [
                'label' => 'En cours de paiement ',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'valide'])
            ],
            [
                'label' => 'Dossiers soldés',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'solde'])
            ]
        ];

        return $this->render('home/time_index.html.twig', [
            'modules' => $modules,
        ]);
    }
    #[Route('/workflow/etudiant/{id}/formation', name: 'app_home_timeline_etudiant_formation_index')]
    public function indexEtudiantFormation(Inscription $inscription): Response
    {

        $modules = [
            /*  [
                'label' => 'En attente de paiement préinscription',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_config_preinscription_suivi_formation_index', ['id' => $inscription->getId()])
                //'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_index')
            ],
            [
                'label' => 'Traitement après examen',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_deliberation_preinscription_formation_index', ['id' => $inscription->getId()])
            ], */
            [
                'label' => 'En attente échéancier de paiement',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'etat' => in_array($inscription->getEtat(), ['attente_echeancier', 'echeance_soumis', 'rejete'])  == true ? true : false,
                'href' => $this->generateUrl('app_inscription_inscription_suivi_formation_index', ['etat' => 'attente_echeance', 'id' => $inscription->getId()])
            ],
            [
                'label' => 'En cours de paiement ',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'etat' => $inscription->getEtat() == 'valide' ? true : false,
                'href' => $this->generateUrl('app_inscription_inscription_suivi_formation_index', ['etat' => 'valide', 'id' => $inscription->getId()])
            ],
            [
                'label' => 'Dossiers soldés',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'etat' => $inscription->getEtat() == 'solde' ? true : false,
                'href' => $this->generateUrl('app_inscription_inscription_suivi_formation_index', ['etat' => 'solde', 'id' => $inscription->getId()])
            ]
        ];

        return $this->render('home/time_formation_index.html.twig', [
            'modules' => $modules,
            'inscription' => $inscription
        ]);
    }
    #[Route('/workflow/etudiant/{id}/formation/preinscription', name: 'app_home_timeline_etudiant_formation_preinscription_index')]
    public function indexEtudiantFormationPreinscription(Preinscription $inscription): Response
    {

        $modules = [
            [
                'label' => 'En attente de paiement préinscription',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'etat' => in_array($inscription->getEtat(), ['attente_paiement', 'attente_informations', 'rejete', 'attente_validation'])  == true ? true : false,
                'href' => $this->generateUrl('app_config_preinscription_suivi_formation_index', ['id' => $inscription->getId()])
                //'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_index')
            ],
            [
                'label' => 'Traitement après examen',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'etat' => $inscription->getEtat() == 'valide' ? true : false,
                'href' => $this->generateUrl('app_deliberation_preinscription_formation_index', ['id' => $inscription->getId()])
            ],
            /* [
                'label' => 'Préinscriptions payées',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'etat' => $inscription->getEtat() == 'valide' ? true : false,
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_solde_index', ['id' => $inscription->getId()])
            ],  */
        ];

        return $this->render('home/time_formation_preinscription_index.html.twig', [
            'modules' => $modules,
            'inscription' => $inscription
        ]);
    }



    #[Route(path: '/verification/validation/dossier/{id}/{preinscription}', name: 'verification_validation_dossier', methods: ['GET', 'POST'])]
    public function information(Request $request, $preinscription, UserInterface $user, Etudiant $etudiant, PreinscriptionRepository $preinscriptionRepository, PersonneRepository $personneRepository, FormError $formError, NiveauRepository $niveauRepository, UtilisateurRepository $utilisateurRepository): Response
    {
        //$etudiant = $user->getPersonne();

        //dd($niveauRepository->findNiveauDisponible(21));

        //dd($preinscription);

        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(EtudiantVerificationType::class, $etudiant, [
            'method' => 'POST',
            'type' => 'info',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('verification_validation_dossier', ['id' => $etudiant->getId(), 'preinscription' => $preinscription]),
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            $preinscriptionData = $preinscriptionRepository->find($preinscription);



            if ($form->isValid()) {

                if ($form->getClickedButton()->getName() === 'valider') {
                    $preinscriptionData->setEtat('attente_validation');
                    $etudiant->setEtat('complete');
                    $preinscriptionRepository->add($preinscriptionData, true);
                }

                $personneRepository->add($etudiant, true);

                //$entityManager->flush();

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

        return $this->render('site/informations_verification.html.twig', [
            'etudiant' => $etudiant,
            'preinscription' => $preinscriptionRepository->find($preinscription),
            'form' => $form->createView(),
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }

    #[Route('/{id}/rejeter', name: 'app_demande_rejeter', methods: ['GET', 'POST'])]
    public function Rejeter(Request $request, Security $security, DecisionRepository $decisionRepository, Preinscription $preinscription, PreinscriptionRepository $preinscriptionRepository, FormError $formError, Registry $workflow, EtudiantRepository $etudiantRepository): Response
    {
        //dd();

        /*  foreach ($preinscription->getDecisions() as $key => $value) {

            $preinscription->clearDecision($value);
        } */
        $decision = new Decision();

        $decision->setDateCreation(new DateTime());
        $decision->setUtilisateur($security->getUser());
        $decision->setCommentaire('RAS');
        $decision->setDecision('null');
        $preinscription->addDecision($decision);

        $preinscription->setDateValidation(new \DateTime());
        $form = $this->createForm(PreinscriptionValidationType::class, $preinscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_demande_rejeter', [
                'id' =>  $preinscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $decisions = $form->get('decisions')->getData();

            //dd($decision);

            // dd($etat);
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            $workflow_data = $workflow->get($preinscription, 'preinscription');


            if ($form->isValid()) {

                foreach ($decisions as $key => $decision) {
                    if ($decision->getDecision() == 'Valider') {
                        $preinscription->setEtat('attente_paiement');
                    } elseif ($decision->getDecision() == 'Recaler') {
                        $preinscription->setEtat('rejete');
                    } else {
                        $preinscription->setEtat('attente_informations');

                        $etudiant = $preinscription->getEtudiant();

                        $etudiant->setEtat('pas_complet');
                        $etudiantRepository->add($etudiant, true);
                    }

                    $preinscription->setCommentaire($decision->getCommentaire());
                }

                /*  */
                $preinscriptionRepository->add($preinscription, true);
                // $demandeRepository->save($demande, true);



                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $fullRedirect = true;
                $this->addFlash('success', $message);
                //$this->redirectToRoute("app_config_workflow_index");
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
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

        return $this->render('comptabilite/preinscription/validataion.html.twig', [
            'preinscription' => $preinscription,
            // 'fichiers' => $repository->findOneBySomeFields($demande),
            'form' => $form->createView(),
            'decisions' => $decisionRepository->findBy(['preinscription' => $preinscription]),
        ]);
    }

    #[Route('/preinscription/solde', name: 'app_comptabilite_niveau_etudiant_preinscription_solde_index', methods: ['GET', 'POST'])]
    public function indexPreinscriptionSolde(Request $request, DataTableFactory $dataTableFactory, UserInterface $user, SessionInterface $session): Response
    {

        //dd('vv');
        $ver = $this->isGranted('ROLE_ETUDIANT');
        $anneeScolaire = $session->get('anneeScolaire');
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code Preinscription'])
            ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                return   $preinscription->getEtudiant()->getNomComplet();
            }])
            /* ->add('etudiant', TextColumn::class, ['field' => 'etudiant.nom', 'label' => 'Nom'])
            ->add('prenoms', TextColumn::class, ['field' => 'etudiant.prenom', 'label' => 'Prénoms']) */
            ->add('dateNaissance', DateTimeColumn::class, ['label' => 'Date de naissance', 'format' => 'd-m-Y', "searchable" => false, 'field' => 'etudiant.dateNaissance'])
            ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
            ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date pré-inscription', 'format' => 'd/m/Y', "searchable" => false,])
            /*   ->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']) */
            //->add('montantPreinscription', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Preinscription::class,
                'query' => function (QueryBuilder $qb) use ($user, $ver, $anneeScolaire) {
                    $qb->select('e, filiere, etudiant,niveau,c')
                        ->from(Preinscription::class, 'e')
                        ->join('e.etudiant', 'etudiant')
                        ->join('e.niveau', 'niveau')
                        /* ->join('niveau', 'niveau') */
                        ->join('niveau.filiere', 'filiere')
                        ->leftJoin('e.caissiere', 'c')
                        ->andWhere('e.etat = :statut')
                        ->setParameter('statut', 'valide');

                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->andWhere('e.etudiant = :etudiant')
                            ->setParameter('etudiant', $user->getPersonne());
                    }
                    if ($anneeScolaire != null) {

                        $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                            ->setParameter('anneeScolaire', $anneeScolaire);
                    }
                }
            ])
            ->setName('dt_app_comptabilite_niveau_etudiant_preinscription_solde');
        // dd($this->isGranted('ROLE_ETUDIANT'));
        $renders = [
            'edit' => new ActionRender(fn () => $ver == false),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'show' => new ActionRender(function () {
                return true;
            }),
            'imprime' => new ActionRender(function () {
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Preinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'imprime' => [
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
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_comptabilite_preinscription_show', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-eye',
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


        return $this->render('comptabilite/niveau_etudiant/index_solde.html.twig', [
            'datatable' => $table
        ]);
    }



    #[Route('/admin/frais', name: 'app_inscription_inscription_frais_index', methods: ['GET', 'POST'])]
    public function indexListe(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, SessionInterface $session): Response
    {
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $isRoleFind = $this->isGranted('ROLE_SECRETAIRE');
        $isRoleAdminFind = $this->isGranted('ROLE_ADMIN');
        $etat = 'valide';

        $anneeScolaire = $session->get('anneeScolaire');


        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code étudiant'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.code', 'label' => 'Code Niveau'])
            ->add('classe', TextColumn::class, ['field' => 'classe.libelle', 'label' => 'Classe']);

        if (!$isEtudiant) {
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms étudiant', 'render' => function ($value, Inscription $inscription) {
                    return $inscription->getEtudiant()->getNomComplet();
                }]);
        }
        // if ($etat == 'valide') {
        $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant à payer', 'field' => 'p.montant'])
            ->add('solde', TextColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $inscription) {
                return (int)$inscription->getMontant() - (int)$inscription->getTotalPaye();
            }]);
        //  $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'p.datePaiement', 'format' => 'd-m-Y']);
        // }
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat, $anneeScolaire) {
                $qb->select(['p'])
                    /* $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant', 'classe', 'promotion']) */
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('p.classe', 'classe')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }

                $qb->andWhere('p.etat = :etat')
                    ->setParameter('etat', $etat);

                if ($anneeScolaire != null) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_frais');

        $renders = [
            /* 'edit' =>  new ActionRender(function () {
                return true;
            }), */
            'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeancier' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'),
            'payer' => new ActionRender(fn () => $etat == 'valide'),
            'payer_load' => new ActionRender(fn () => $etat == 'valide'),
            'delete' => new ActionRender(fn () =>  $isRoleFind == true || $isRoleAdminFind == true),
            /*  'delete' => new ActionRender(function () {
                return ;
            }), */
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'recu' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_seconde',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack']
                                //'render' => new ActionRender(fn() => $context->getEtat() != 'cree')
                            ],
                            /*  'payer' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ], */
                            'payer_load' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('app_config_inscription_frais_scolarite_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer_load']
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
                            /*  'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ] */
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


        return $this->render('inscription/inscription/index_frais.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'type' => 'frais',
            //'titre' => $etat == 'valide' ? : 'liste des frais',
        ]);
    }
    #[Route('/admin/frais/solde', name: 'app_inscription_inscription_frais_solde_index', methods: ['GET', 'POST'])]
    public function indexListeFraisSolde(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, SessionInterface $session): Response
    {
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $isRoleFind = $this->isGranted('ROLE_SECRETAIRE');
        $isRoleAdminFind = $this->isGranted('ROLE_ADMIN');
        $etat = 'solde';

        $anneeScolaire = $session->get('anneeScolaire');


        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code étudiant'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.code', 'label' => 'Code Niveau'])
            ->add('classe', TextColumn::class, ['field' => 'classe.libelle', 'label' => 'Classe']);

        if (!$isEtudiant) {
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms étudiant', 'render' => function ($value, Inscription $inscription) {
                    return $inscription->getEtudiant()->getNomComplet();
                }]);
        }
        // if ($etat == 'valide') {
        $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant à payer', 'field' => 'p.montant'])
            ->add('solde', TextColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $inscription) {
                return (int)$inscription->getMontant() - (int)$inscription->getTotalPaye();
            }]);
        //  $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'p.datePaiement', 'format' => 'd-m-Y']);
        // }
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat, $anneeScolaire) {
                $qb->select(['p'])
                    /* $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant', 'classe', 'promotion']) */
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('p.classe', 'classe')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }

                $qb->andWhere('p.etat = :etat')
                    ->setParameter('etat', $etat);

                if ($anneeScolaire != null) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_frais_solde');

        $renders = [
            /* 'edit' =>  new ActionRender(function () {
                return true;
            }), */
            'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeancier' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'),
            'payer' => new ActionRender(fn () => $etat == 'valide'),
            'payer_load' => new ActionRender(fn () => $etat == 'valide'),
            'delete' => new ActionRender(fn () =>  $isRoleFind == true || $isRoleAdminFind == true),
            /*  'delete' => new ActionRender(function () {
                return ;
            }), */
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'recu' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_seconde',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack']
                                //'render' => new ActionRender(fn() => $context->getEtat() != 'cree')
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


        return $this->render('inscription/inscription/index_frais_solde.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'type' => 'frais',
            //'titre' => $etat == 'valide' ? : 'liste des frais',
        ]);
    }
    #[Route('/admin/frais/caissiere', name: 'app_inscription_inscription_frais_caissiere_index', methods: ['GET', 'POST'])]
    public function indexListeCaissiere(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, SessionInterface $session): Response
    {
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $isRoleFind = $this->isGranted('ROLE_SECRETAIRE');
        $isRoleAdminFind = $this->isGranted('ROLE_ADMIN');
        $etat = 'valide';
        $anneeScolaire = $session->get('anneeScolaire');

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code étudiant'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.code', 'label' => 'Code Niveau'])
            ->add('classe', TextColumn::class, ['field' => 'classe.libelle', 'label' => 'Classe']);
        /* ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
            ->add('dateInscription', DateTimeColumn::class, ['label' => 'Date création', 'format' => 'd-m-Y']); */

        if ($etat != 'attente_echeancier') {

            //$table->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']);
        }


        if (!$isEtudiant) {
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms étudiant', 'render' => function ($value, Inscription $inscription) {
                    return $inscription->getEtudiant()->getNomComplet();
                }]);
        }
        if ($etat == 'valide') {
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant à payer', 'field' => 'p.montant'])
                ->add('solde', TextColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $inscription) {
                    return (int)$inscription->getMontant() - (int)$inscription->getTotalPaye();
                }]);
            //  $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'p.datePaiement', 'format' => 'd-m-Y']);
        }
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat, $anneeScolaire) {
                $qb->select(['p'])
                    /* $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant', 'classe', 'promotion']) */
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('p.classe', 'classe')
                    ->join('niveau.filiere', 'filiere')
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
                        ->setParameter('etat', 'valide');
                }

                if ($anneeScolaire != null) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
                if ($anneeScolaire != null) {

                    $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                        ->setParameter('anneeScolaire', $anneeScolaire);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_frais_caissiere');

        $renders = [
            /* 'edit' =>  new ActionRender(function () {
                return true;
            }), */
            'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeancier' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'),
            'payer' => new ActionRender(fn () => $etat == 'valide'),
            'payer_load' => new ActionRender(fn () => $etat == 'valide'),
            'delete' => new ActionRender(fn () =>  $isRoleFind == true || $isRoleAdminFind == true),
            /*  'delete' => new ActionRender(function () {
                return ;
            }), */
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'recu' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_seconde',
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
                            /*  'payer' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ], */
                            'payer_load' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('app_config_inscription_frais_scolarite_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer_load']
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
                            /*  'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ] */
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


        return $this->render('inscription/inscription/index_frais_caissiere.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'type' => 'caissiere',
        ]);
    }
}
