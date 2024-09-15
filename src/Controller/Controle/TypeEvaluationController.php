<?php

namespace App\Controller\Controle;

use App\Entity\TypeEvaluation;
use App\Form\TypeEvaluationType;
use App\Repository\AnneeScolaireRepository;
use App\Repository\TypeEvaluationRepository;
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

#[Route('/admin/controle/type/evaluation')]
class TypeEvaluationController extends AbstractController
{
    #[Route('/liste/type/evaluation', name: 'get_type_evaluation', methods: ['GET'])]
    public function getmatiere(Request $request, TypeEvaluationRepository $typeEvaluationRepository)
    {
        $response = new Response();
        $tabTypeContrrole = array();

        $typeControles = $typeEvaluationRepository->findAll();


        //dd($tabTypeContrrole);
        $i = 0;

        foreach ($typeControles as $e) {
            // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
            $tabTypeContrrole[$i]['id'] = $e->getId();
            $tabTypeContrrole[$i]['libelle'] = $e->getCode();
            $i++;
        }

        $dataService = json_encode($tabTypeContrrole); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($dataService);

        return $response;
    }

    #[Route('/', name: 'app_controle_type_evaluation_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, SessionInterface $session, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $annee = $session->get('anneeScolaire');


        if ($annee == null) {

            $session->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'libelle'])
            ->add('coef', TextColumn::class, ['label' => 'Coefficient'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => TypeEvaluation::class,
            ])
            ->setName('dt_app_controle_type_evaluation');

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
                'render' => function ($value, TypeEvaluation $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_controle_type_evaluation_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_controle_type_evaluation_delete', ['id' => $value]),
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


        return $this->render('controle/type_evaluation/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_controle_type_evaluation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $typeEvaluation = new TypeEvaluation();
        $form = $this->createForm(TypeEvaluationType::class, $typeEvaluation, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_type_evaluation_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_type_evaluation_index');




            if ($form->isValid()) {

                $entityManager->persist($typeEvaluation);
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

        return $this->render('controle/type_evaluation/new.html.twig', [
            'type_evaluation' => $typeEvaluation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_controle_type_evaluation_show', methods: ['GET'])]
    public function show(TypeEvaluation $typeEvaluation): Response
    {
        return $this->render('controle/type_evaluation/show.html.twig', [
            'type_evaluation' => $typeEvaluation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_controle_type_evaluation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeEvaluation $typeEvaluation, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(TypeEvaluationType::class, $typeEvaluation, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_type_evaluation_edit', [
                'id' =>  $typeEvaluation->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_type_evaluation_index');




            if ($form->isValid()) {

                $entityManager->persist($typeEvaluation);
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

        return $this->render('controle/type_evaluation/edit.html.twig', [
            'type_evaluation' => $typeEvaluation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_controle_type_evaluation_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, TypeEvaluation $typeEvaluation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_controle_type_evaluation_delete',
                    [
                        'id' => $typeEvaluation->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($typeEvaluation);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_controle_type_evaluation_index');

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

        return $this->render('controle/type_evaluation/delete.html.twig', [
            'type_evaluation' => $typeEvaluation,
            'form' => $form,
        ]);
    }
}
