<?php

namespace App\Controller\EncartBac;

use App\Entity\EncartBac;
use App\Form\EncartBacType;
use App\Repository\EncartBacRepository;
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
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/encartbac/encart/bac')]
class EncartBacController extends AbstractController
{
    #[Route('/', name: 'app_encartbac_encart_bac_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->createAdapter(ORMAdapter::class, [
            'entity' => EncartBac::class,
        ])
        ->setName('dt_app_encartbac_encart_bac');

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
                'label' => 'Actions'
                , 'orderable' => false
                ,'globalSearchable' => false
                ,'className' => 'grid_row_actions'
                , 'render' => function ($value, EncartBac $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_encartbac_encart_bac_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#modal-small',
                            'url' => $this->generateUrl('app_encartbac_encart_bac_delete', ['id' => $value]),
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


        return $this->render('encartbac/encart_bac/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_encartbac_encart_bac_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $encartBac = new EncartBac();
        $form = $this->createForm(EncartBacType::class, $encartBac, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_encartbac_encart_bac_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_encartbac_encart_bac_index');




            if ($form->isValid()) {

                $entityManager->persist($encartBac);
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
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }


        }

        return $this->render('encartbac/encart_bac/new.html.twig', [
            'encart_bac' => $encartBac,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_encartbac_encart_bac_show', methods: ['GET'])]
    public function show(EncartBac $encartBac): Response
    {
        return $this->render('encartbac/encart_bac/show.html.twig', [
            'encart_bac' => $encartBac,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_encartbac_encart_bac_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EncartBac $encartBac, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(EncartBacType::class, $encartBac, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_encartbac_encart_bac_edit', [
                    'id' =>  $encartBac->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

       if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_encartbac_encart_bac_index');




            if ($form->isValid()) {

                $entityManager->persist($encartBac);
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
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }

        }

        return $this->render('encartbac/encart_bac/edit.html.twig', [
            'encart_bac' => $encartBac,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_encartbac_encart_bac_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, EncartBac $encartBac, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_encartbac_encart_bac_delete'
                ,   [
                        'id' => $encartBac->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($encartBac);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_encartbac_encart_bac_index');

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

        return $this->render('encartbac/encart_bac/delete.html.twig', [
            'encart_bac' => $encartBac,
            'form' => $form,
        ]);
    }
}
