<?php

namespace App\Controller\Controle;

use App\Entity\Cours;
use App\Entity\CoursParent;
use App\Form\CoursParentType;
use App\Repository\AnneeScolaireRepository;
use App\Repository\ClasseRepository;
use App\Repository\CoursParentRepository;
use App\Repository\CoursRepository;
use App\Repository\MatiereRepository;
use App\Repository\MatiereUeRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
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

#[Route('/admin/controle/cours/parent')]
class CoursParentController extends AbstractController
{
    #[Route('/', name: 'app_controle_cours_parent_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->createAdapter(ORMAdapter::class, [
                'entity' => CoursParent::class,
            ])
            ->setName('dt_app_controle_cours_parent');

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
                'render' => function ($value, CoursParent $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_controle_cours_parent_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_controle_cours_parent_delete', ['id' => $value]),
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


        return $this->render('controle/cours_parent/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_controle_cours_parent_new', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        CoursParentRepository $coursParentRepository,
        CoursRepository $coursRepository,
        MatiereUeRepository $matiereUeRepository,
        MatiereRepository $matiereRepository,
        SessionInterface $session,
        ClasseRepository $classeRepository,
        AnneeScolaireRepository $anneeScolaireRepository
    ): Response {

        $classe =  $request->query->get('classe');
        $annee = $session->get('anneeScolaire');
        $all = $request->query->all();
        //dd($all);

        $annee = $session->get('anneeScolaire');


        if ($annee == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }

        $coursParentVerification = $coursParentRepository->findOneBy(['classe' => $classe]);
        if ($coursParentVerification) {
            //dd($coursParentVerification);

            $form = $this->createForm(CoursParentType::class, $coursParentVerification, [
                'method' => 'POST',
                'anneeScolaire' => $session->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_cours_parent_new')
            ]);
        } else {
            $coursParent = new CoursParent();

            $matieresUe = $matiereUeRepository->getAllMatiere($classe, $annee);


            foreach ($matieresUe as $key => $matiereUe) {
                // dd($matiereUe->getMatiere());
                // if ($matiereUe->getUniteEnseignement()->getNiveau() == $classeRepository->find($classe)->getNiveau()) {

                $cours = new Cours();
                $cours->setMatiere($matiereUe->getMatiere());
                $cours->setAnneeScolaire($annee);
                //  $cours->setClasse($classeRepository->find($classe));

                $coursParent->addCour($cours);
                //}
            }

            $form = $this->createForm(CoursParentType::class, $coursParent, [
                'method' => 'POST',
                'anneeScolaire' => $session->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_cours_parent_new')
            ]);
        }


        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_cours_parent_new');




            if ($form->isValid()) {


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

        return $this->render('controle/cours_parent/new.html.twig', [
            'cours_parent' => $coursParentVerification ?? $coursParent,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/new/load/{classe}', name: 'app_controle_cours_parent_new_load', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function newLoad(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        CoursParentRepository $coursParentRepository,
        CoursRepository $coursRepository,
        MatiereUeRepository $matiereUeRepository,
        MatiereRepository $matiereRepository,
        SessionInterface $session,
        ClasseRepository $classeRepository,
        $classe,
    ): Response {


        $annee = $session->get('anneeScolaire');
        $all = $request->query->all();


        $coursParentVerification = $coursParentRepository->findOneBy(['classe' => $classe]);
        if ($coursParentVerification) {
            //dd($coursParentVerification);

            $form = $this->createForm(CoursParentType::class, $coursParentVerification, [
                'method' => 'POST',
                'anneeScolaire' => $session->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_cours_parent_new_load', [
                    'classe' => $classe
                ])
            ]);
        } else {
            $coursParent = new CoursParent();

            $matieresUe = $matiereUeRepository->getAllMatiereWithouLimit($classe, $annee);

            //dd($matieresUe);

            foreach ($matieresUe as $key => $matiereUe) {
                // dd($matiereUe->getMatiere());
                if ($matiereUe->getUniteEnseignement()->getNiveau() == $classeRepository->find($classe)->getNiveau()) {

                    $cours = new Cours();
                    $cours->setMatiere($matiereUe->getMatiere());
                    $cours->setAnneeScolaire($annee);
                    $cours->setClasse($classeRepository->find($classe));

                    $coursParent->addCour($cours);
                }
            }

            $form = $this->createForm(CoursParentType::class, $coursParent, [
                'method' => 'POST',
                'anneeScolaire' => $session->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_cours_parent_new_load', [
                    'classe' => $classe
                ])
            ]);
        }


        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_cours_parent_new');




            if ($form->isValid()) {

                $entityManager->persist($coursParent);
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

        return $this->render('controle/cours_parent/new_load.html.twig', [
            'cours_parent' => $coursParentVerification ?? $coursParent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_controle_cours_parent_show', methods: ['GET'])]
    public function show(CoursParent $coursParent): Response
    {
        return $this->render('controle/cours_parent/show.html.twig', [
            'cours_parent' => $coursParent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_controle_cours_parent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CoursParent $coursParent, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(CoursParentType::class, $coursParent, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_cours_parent_edit', [
                'id' =>  $coursParent->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_cours_parent_index');




            if ($form->isValid()) {

                $entityManager->persist($coursParent);
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

        return $this->render('controle/cours_parent/edit.html.twig', [
            'cours_parent' => $coursParent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_controle_cours_parent_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, CoursParent $coursParent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_controle_cours_parent_delete',
                    [
                        'id' => $coursParent->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($coursParent);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_controle_cours_parent_index');

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

        return $this->render('controle/cours_parent/delete.html.twig', [
            'cours_parent' => $coursParent,
            'form' => $form,
        ]);
    }
}
