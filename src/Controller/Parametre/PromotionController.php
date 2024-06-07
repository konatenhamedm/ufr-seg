<?php

namespace App\Controller\Parametre;

use App\Entity\Frais;
use App\Entity\Promotion;
use App\Entity\TypeFrais;
use App\Form\PromotionType;
use App\Repository\FraisRepository;
use App\Repository\PromotionRepository;
use App\Service\ActionRender;
use App\Service\FormError;
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
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/parametre/promotion')]
class PromotionController extends AbstractController
{
    #[Route('/', name: 'app_parametre_promotion_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.code'])
            ->add('anneeScolaire', TextColumn::class, ['label' => 'Année scolaire', 'field' => 'a.libelle'])
            ->add('numero', TextColumn::class, ['label' => 'Numéro'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Promotion::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select(['c'])
                        ->from(Promotion::class, 'c')
                        ->innerJoin('c.anneeScolaire', 'a')
                        ->innerJoin('c.niveau', 'niveau')
                        ->orderBy('c.id', 'DESC');
                }
            ])
            ->setName('dt_app_parametre_promotion');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Promotion $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_promotion_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_promotion_delete', ['id' => $value]),
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


        return $this->render('parametre/promotion/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_parametre_promotion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $promotion = new Promotion();
        $typeFrais = $entityManager->getRepository(TypeFrais::class)->findAll();
        foreach ($typeFrais as $type) {
            $frais = new Frais();
            $frais->setTypeFrais($type);
            $promotion->addFrai($frais);
        }
        $form = $this->createForm(PromotionType::class, $promotion, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_promotion_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_promotion_index');




            if ($form->isValid()) {

                $entityManager->persist($promotion);
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

        return $this->render('parametre/promotion/new.html.twig', [
            'promotion' => $promotion,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_promotion_show', methods: ['GET'])]
    public function show(Promotion $promotion): Response
    {
        return $this->render('parametre/promotion/show.html.twig', [
            'promotion' => $promotion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_promotion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Promotion $promotion, EntityManagerInterface $entityManager, FormError $formError, FraisRepository $fraisRepository): Response
    {
        // dd($promotion);
        // $typeFrais = $entityManager->getRepository(TypeFrais::class)->findBy(['promotion' => $promotion]);
        foreach ($promotion->getFrais() as $frais) {

            $frais->setTypeFrais($frais->getTypeFrais());
            $frais->setMontant($frais->getMontant());
            //$frais->s($type);
            $promotion->addFrai($frais);
        }
        foreach ($promotion->getFrais() as $frais) {

            $frais->setTypeFrais($frais->getTypeFrais());
            //$frais->s($type);
            $promotion->addFrai($frais);
        }
        $form = $this->createForm(PromotionType::class, $promotion, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_promotion_edit', [
                'id' =>  $promotion->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_promotion_index');




            if ($form->isValid()) {

                $entityManager->persist($promotion);
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

        return $this->render('parametre/promotion/edit.html.twig', [
            'promotion' => $promotion,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_promotion_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_promotion_delete',
                    [
                        'id' => $promotion->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($promotion);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_promotion_index');

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

        return $this->render('parametre/promotion/delete.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
        ]);
    }
}
