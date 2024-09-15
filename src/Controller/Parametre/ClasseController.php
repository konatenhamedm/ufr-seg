<?php

namespace App\Controller\Parametre;

use App\Entity\Classe;
use App\Entity\Cours;
use App\Entity\CoursParent;
use App\Form\ClasseType;
use App\Form\NiveauAddEnseignantType;
use App\Repository\AnneeScolaireRepository;
use App\Repository\ClasseRepository;
use App\Repository\CoursParentRepository;
use App\Repository\CoursRepository;
use App\Repository\MatiereUeRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use DateTime;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/admin/parametre/classe')]
class ClasseController extends AbstractController
{
    #[Route('/', name: 'app_parametre_classe_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, UserInterface $user, SessionInterface $session): Response
    {
        $anneeScolaire = $session->get('anneeScolaire');
        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle'])
            ->add('annee', TextColumn::class, ['label' => 'Année', 'field' => 'annee.libelle'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Classe::class,
                'query' => function (QueryBuilder $qb) use ($user, $anneeScolaire) {
                    $qb->select('u, niveau, annee,res')
                        ->from(Classe::class, 'u')
                        ->join('u.niveau', 'niveau')
                        ->leftJoin('niveau.responsable', 'res')
                        ->join('u.anneeScolaire', 'annee');

                    if ($user->getPersonne()->getFonction()->getCode() == 'DR') {
                        $qb->andWhere("res = :user")
                            ->setParameter('user', $user->getPersonne());
                    }

                    if ($anneeScolaire != null) {

                        $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                            ->setParameter('anneeScolaire', $anneeScolaire);
                    }
                }
            ])
            ->setName('dt_app_parametre_classe');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'personnalise' =>  new ActionRender(function () {
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
                'render' => function ($value, Classe $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_classe_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'personnalise' => [
                                'url' => $this->generateUrl('app_parametre_niveau_enseignant_matiere_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-person-plus',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['personnalise']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_classe_delete', ['id' => $value]),
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


        return $this->render('parametre/classe/index.html.twig', [
            'datatable' => $table
        ]);
    }
    #[Route('/imprime/filtre', name: 'app_parametre_classe_imprime_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexImprime(Request $request, DataTableFactory $dataTableFactory, UserInterface $user, SessionInterface $session): Response
    {
        $anneeScolaire = $session->get('anneeScolaire');

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_parametre_classe_imprime_index', [
                /* 'etat' => $etat */])
        ])->add('classe', EntityType::class, [
            'class' => Classe::class,
            'multiple' => true,
            'choice_label' => 'libelle',
            'label' => 'Classes',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2'],
            'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                return $er->createQueryBuilder('c')
                    ->andWhere('c.anneeScolaire = :anneeScolaire')
                    ->setParameter('anneeScolaire', $anneeScolaire)
                    ->orderBy('c.id', 'DESC');
            },
        ])
            ->add(
                'etat',
                ChoiceType::class,
                [
                    'placeholder' => "Choisir un type d'etat",
                    'label' => 'Privilèges Supplémentaires',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'ETAT_CLASSE' => 'Liste des classes détaillés',
                        'ETAT_PRESENCE' => 'Liste de présence',
                        'ETAT_NOTE' => 'Fiche des notes',

                    ]),
                ]
            );
        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.code'])
            ->add('annee', TextColumn::class, ['label' => 'Année', 'field' => 'annee.libelle'])
            ->add('effectif', TextColumn::class, ['label' => 'Effectif', 'className' => 'text-center w-5', 'render' => function ($value, Classe $context) {
                // if ($value == 'valide') {
                return "52";
                // }
                // return sprintf('<span class="badge badge-success">Ajourné(e)</span>');
            }])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Classe::class,
                'query' => function (QueryBuilder $qb) use ($user, $anneeScolaire) {
                    $qb->select('u, niveau, annee,res')
                        ->from(Classe::class, 'u')
                        ->join('u.niveau', 'niveau')
                        ->leftJoin('niveau.responsable', 'res')
                        ->join('u.anneeScolaire', 'annee');

                    if ($user->getPersonne()->getFonction()->getCode() == 'DR') {
                        $qb->andWhere("res = :user")
                            ->setParameter('user', $user->getPersonne());
                    }

                    if ($anneeScolaire != null) {

                        $qb->andWhere('niveau.anneeScolaire = :anneeScolaire')
                            ->setParameter('anneeScolaire', $anneeScolaire);
                    }
                }
            ])
            ->setName('dt_app_parametre_classe_imprime');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'personnalise' =>  new ActionRender(function () {
                return true;
            }),

            'delete' => new ActionRender(function () {
                return true;
            }),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = false;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions',
                'orderable' => false,
                'globalSearchable' => false,
                'className' => 'grid_row_actions',
                'render' => function ($value, Classe $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_classe_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'personnalise' => [
                                'url' => $this->generateUrl('app_parametre_niveau_enseignant_matiere_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-person-plus',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['personnalise']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_classe_delete', ['id' => $value]),
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


        return $this->render('parametre/classe/index_imprimer_filtre.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm(),

        ]);
    }



    #[Route('/{id}/enseignant/matiere/edit/', name: 'app_parametre_niveau_enseignant_matiere_edit', methods: ['GET', 'POST'])]
    public function editPersonnalisation(Request $request, Classe $classe, EntityManagerInterface $entityManager, CoursParentRepository $coursParentRepository, SessionInterface $session, MatiereUeRepository $matiereUeRepository, ClasseRepository $classeRepository, FormError $formError, CoursRepository $coursRepository, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $annee = $session->get('anneeScolaire');
        $matieresUe = $matiereUeRepository->getAllMatiereWithouLimit($classe, $annee);

        //dd($annee);
        // dd($coursRepository->findBy(['classe' => $classe, 'anneeScolaire' => $annee]));

        if ($coursRepository->findBy(['classe' => $classe, 'anneeScolaire' => $annee]) == null) {
            $coursParent = new CoursParent();
            $coursParent->setClasse($classe);

            foreach ($matieresUe as $key => $matiereUe) {
                // dd($matiereUe->getMatiere());
                if ($matiereUe->getUniteEnseignement()->getNiveau() == $classeRepository->find($classe)->getNiveau()) {

                    //dd($matiereUe->getMatiere());

                    // $coursParentRepository->add($coursParent, true);

                    $cours = new Cours();
                    $cours->setMatiere($matiereUe->getMatiere());
                    $cours->setAnneeScolaire($anneeScolaireRepository->find($annee->getId()));
                    $cours->setCoursParent($coursParent);
                    /* $cours->setClasse($classeRepository->find($classe)); */

                    $classe->addCoursParent($coursParent);
                    $classe->addCour($cours);
                }
            }
        }



        $form = $this->createForm(NiveauAddEnseignantType::class, $classe, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_niveau_enseignant_matiere_edit', [
                'id' =>  $classe->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_classe_index');

            $data = $form->get('cours')->getData();



            if ($form->isValid()) {



                foreach ($data as $key => $cours) {
                    $cours->setAnneeScolaire($anneeScolaireRepository->findOneBy(['actif' => true]));

                    $coursRepository->add($cours, true);
                }
                $entityManager->persist($classe);
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

        return $this->render('parametre/classe/enseignant_matiere.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/new', name: 'app_parametre_classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, SessionInterface $session): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe, [
            'method' => 'POST',
            "anneeScolaire" => $session->get('anneeScolaire'),
            'action' => $this->generateUrl('app_parametre_classe_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_classe_index');




            if ($form->isValid()) {

                $entityManager->persist($classe);
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

        return $this->render('parametre/classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_classe_show', methods: ['GET'])]
    public function show(Classe $classe): Response
    {
        return $this->render('parametre/classe/show.html.twig', [
            'classe' => $classe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, EntityManagerInterface $entityManager, FormError $formError, SessionInterface $session): Response
    {

        $form = $this->createForm(ClasseType::class, $classe, [
            'method' => 'POST',
            "anneeScolaire" => $session->get('anneeScolaire'),
            'action' => $this->generateUrl('app_parametre_classe_edit', [
                'id' =>  $classe->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_classe_index');




            if ($form->isValid()) {

                $entityManager->persist($classe);
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

        return $this->render('parametre/classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/delete', name: 'app_parametre_classe_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_classe_delete',
                    [
                        'id' => $classe->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($classe);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_classe_index');

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

        return $this->render('parametre/classe/delete.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }
}
