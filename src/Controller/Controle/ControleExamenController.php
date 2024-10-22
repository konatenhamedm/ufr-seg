<?php

namespace App\Controller\Controle;

use App\Entity\ControleExamen;
use App\Entity\Decision;
use App\Entity\DecisionExamen;
use App\Entity\GroupeTypeExamen;
use App\Entity\NoteExamen;
use App\Entity\ValeurNoteExamen;
use App\Form\ControleExamenType;
use App\Repository\AnneeScolaireRepository;
use App\Repository\ClasseRepository;
use App\Repository\ControleExamenRepository;
use App\Repository\DecisionExamenRepository;
use App\Repository\DecisionRepository;
use App\Repository\InscriptionRepository;
use App\Repository\SessionRepository;
use App\Repository\TypeControleRepository;
use App\Repository\TypeEvaluationRepository;
use App\Repository\UniteEnseignementRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Service;
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

#[Route('/admin/controle/controle/examen')]
class ControleExamenController extends AbstractController
{

    #[Route('/liste/ue', name: 'get_ue_promotion', methods: ['GET'])]
    public function getUe(Request $request, UniteEnseignementRepository $uniteEnseignementRepository)
    {
        $response = new Response();
        $tabUnite = array();


        $idPromotion = $request->get('id');

        if ($idPromotion) {


            $uniteEnseigenments = $uniteEnseignementRepository->findby(['niveau' => $idPromotion]);

            $i = 0;

            foreach ($uniteEnseigenments as $e) {
                // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabUnite[$i]['id'] = $e->getId();
                $tabUnite[$i]['libelle'] = $e->getLibelle();

                $i++;
            }

            $dataService = json_encode($tabUnite); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);
        }
        return $response;
    }

    #[Route('/liste/session', name: 'get_session', methods: ['GET'])]
    public function getSession(Request $request, SessionRepository $sessionRepository,ClasseRepository $classeRepository)
    {
        $response = new Response();
        $tabUnite = array();


        $idPromotion = $request->get('id');

        if ($idPromotion) {

            $uniteEnseigenments = $sessionRepository->findby(['niveau' => $classeRepository->find($idPromotion)->getNiveau()]);

            $i = 0;

            foreach ($uniteEnseigenments as $e) {
                // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabUnite[$i]['id'] = $e->getId();
                $tabUnite[$i]['libelle'] = $e->getLibelle();

                $i++;
            }

            $dataService = json_encode($tabUnite); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);
        }
        return $response;
    }

    #[Route('/', name: 'app_controle_controle_examen_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->createAdapter(ORMAdapter::class, [
                'entity' => ControleExamen::class,
            ])
            ->setName('dt_app_controle_controle_examen');

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
                'render' => function ($value, ControleExamen $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_controle_controle_examen_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_controle_controle_examen_delete', ['id' => $value]),
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


        return $this->render('controle/controle_examen/index.html.twig', [
            'datatable' => $table
        ]);
    }


    //#[Route('/new', name: 'app_controle_controle_examen_new', methods: ['GET', 'POST'], options: ['expose' => true])]
    #[Route('/new/saisie/simple', name: 'app_controle_controle_examen_new', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        ControleExamenRepository $controleExamenRepository,
        InscriptionRepository $inscriptionRepository,
        TypeControleRepository $typeControleRepository,
        DecisionRepository $decisionRepository,
        SessionRepository $sessionRepository,
        DecisionExamenRepository $decisionExamenRepository,
        SessionInterface $sessionData,
        AnneeScolaireRepository $anneeScolaireRepository
    ): Response {

        $classe = $request->query->get('classe');
        $session = $request->query->get('session');
        $ue = $request->query->get('ue');
        $matiere = $request->query->get('matiere');
 
        $annee = $sessionData->get('anneeScolaire');


        if ($annee == null) {

            $sessionData->set('anneeScolaire', $anneeScolaireRepository->findOneBy(['actif' => 1]));
        }

        $controleVefication = $controleExamenRepository->findOneBy(['classe' => $classe, 'session' => $session, 'ue' => $ue, 'matiere' => $matiere]);

        if ($controleVefication) {
            $form = $this->createForm(ControleExamenType::class, $controleVefication, [
                'method' => 'POST',
                'anneeScolaire' => $sessionData->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_controle_examen_new')
            ]);
        } else {
            $controleExaman = new ControleExamen();
            //dd("");
            $controleExaman->setTypeControle($typeControleRepository->findOneBy(['code' => 'EXA']));

            $groupe = new GroupeTypeExamen();
            $groupe->setDateCompo(new \DateTime());

            // dd($inscriptionRepository->findBy(['niveau' => $promotion]), $promotion);
            if (count($inscriptionRepository->findBy(['classe' => $classe])) > 0)
                $controleExaman->addGroupeTypeExamen($groupe);

            
            foreach ($inscriptionRepository->getListeEtudiant($classe) as $inscription) {
                $note = new NoteExamen();
                $note->setEtudiant($inscription->getEtudiant());
                //$note->setNote('');
                $note->setMoyenneUe('0');
                $note->setMoyenneConrole('0');

                $controleExaman->addNoteExamen($note);
                $valeurNote = new ValeurNoteExamen();
                $valeurNote->setNote("0");
                $note->addValeurNoteExamen($valeurNote);
            }

            $form = $this->createForm(ControleExamenType::class, $controleExaman, [
                'method' => 'POST',
                'anneeScolaire' => $sessionData->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_controle_examen_new')
            ]);
        }



        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_examen_index');




            if ($form->isValid()) {
                /* 
                $entityManager->persist($controleExaman);
                $entityManager->flush(); */

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

        return $this->render('controle/controle_examen/new.html.twig', [
            'controle' => $controleVefication ?? $controleExaman,
            'nombre' => $controleVefication ? $controleVefication->getGroupeTypeExamens()->count() : (count($inscriptionRepository->findBy(['classe' => $classe])) > 0 ? 1 : 0),
            'form' => $form->createView(),
            'title' => 'Gestion des contrôles',
        ]);
    }
    #[Route('/new/load/{session}/{classe}/{ue}/{matiere}', name: 'app_controle_controle_examen_new_load', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function newLoad(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        ControleExamenRepository $controleExamenRepository,
        InscriptionRepository $inscriptionRepository,
        TypeControleRepository $typeControleRepository,
        DecisionRepository $decisionRepository,
        $classe = null,
        $session = null,
        $ue = null,
        $matiere = null,
        SessionRepository $sessionRepository,
        DecisionExamenRepository $decisionExamenRepository,
        Service $service,
        SessionInterface $sessionData
    ): Response {

        $all = $request->query->all();

       
        $controleVefication = $controleExamenRepository->findOneBy(['classe' => $classe,  'session' => $session, 'ue' => $ue,'matiere'=> $matiere]);
        //dd($controleVefication);
        if ($controleVefication) {
            $form = $this->createForm(ControleExamenType::class, $controleVefication, [
                'method' => 'POST',
                'anneeScolaire' => $sessionData->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_controle_examen_new_load', [
                    'session' => $session,
                    'classe' => $classe,
                    'ue' => $ue,
                    'matiere' => $matiere,
                ])
            ]);
        } else {
            $controleExaman = new ControleExamen();

            $controleExaman->setTypeControle($typeControleRepository->findOneBy(['code' => 'EXA']));

            $groupe = new GroupeTypeExamen();
            $groupe->setDateCompo(new \DateTime());

            // dd($inscriptionRepository->findBy(['niveau' => $promotion]), $promotion);
            if (count($inscriptionRepository->findBy(['classe' => $classe])) > 0)
                $controleExaman->addGroupeTypeExamen($groupe);

            // dd($session);
            if ($session != "null") {
                if ((int)$sessionRepository->find($session)->getNumero() == 1) {
                    foreach ($inscriptionRepository->getListeEtudiant($classe) as $inscription) {
                        $note = new NoteExamen();
                        $note->setEtudiant($inscription->getEtudiant());
                        //$note->setNote('');
                        $note->setMoyenneUe('0');
                        $note->setMoyenneConrole('0');

                        $controleExaman->addNoteExamen($note);
                        $valeurNote = new ValeurNoteExamen();
                        $valeurNote->setNote("0");
                        $note->addValeurNoteExamen($valeurNote);
                    }
                } else {

                    foreach ($decisionExamenRepository->findBy(['decision' => DecisionExamen::DECISION['Invalide'], 'classe' => $classe]) as $decision) {
                        $note = new NoteExamen();
                        $note->setEtudiant($decision->getEtudiant());
                        //$note->setNote('');
                        $note->setMoyenneUe('0');
                        $note->setMoyenneConrole('0');

                        $controleExaman->addNoteExamen($note);
                        $valeurNote = new ValeurNoteExamen();
                        $valeurNote->setNote("0");
                        $note->addValeurNoteExamen($valeurNote);
                    }
                }
            }



            $form = $this->createForm(ControleExamenType::class, $controleExaman, [
                'method' => 'POST',
                'anneeScolaire' => $sessionData->get("anneeScolaire"),
                'action' => $this->generateUrl('app_controle_controle_examen_new_load', [
                    'session' => $session,
                    'classe' => $classe,
                    'ue' => $ue,
                    'matiere' => $matiere,
                ])
            ]);
        }



        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;
        $fullRedirect = false;
        $showAlert = false;
        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_examen_new', [
                /* 'session' => $session,
                'niveau' => $niveau,
                'ue' => $ue, */
            ]);

            $dataNotes = $form->get('noteExamens')->getData();
            $groupeTypes = $form->get('groupeTypeExamens')->getData();
        //dd($dataNotes);
            if ($form->isValid()) {



                $compteIfNoteSuperieurMax = $service->gestionNotesExamen($dataNotes, $groupeTypes, ['session' => $session, 'classe' => $classe,  'ue' => $ue,'matiere' => $matiere], $controleVefication ?? null, !$controleVefication ? $controleExaman : null);
                // dd("");
                $service->rangExposantExamen($dataNotes);

                if ($compteIfNoteSuperieurMax > 0) {
                    $showAlert = true;
                    $statut = 0;
                    $message       = sprintf('Désolé veillez bien vérifier les notes saisie il y a au moins une note supperieur a la moyenne max de la notes');
                } else {
                    $message       = 'Opération effectuée avec succès';
                    $statut = 1;
                    $fullRedirect = true;
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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect', 'showAlert'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('controle/controle_examen/new_load.html.twig', [
            'controle' => $controleVefication ?? $controleExaman,
            'nombre' => $controleVefication ? $controleVefication->getGroupeTypeExamens()->count() : (count($inscriptionRepository->findBy(['classe' => $classe])) > 0 ? 1 : 0),
            'form' => $form->createView(),
            'title' => 'Gestion des contrôles',
        ]);
    }

    #[Route('/{id}/show', name: 'app_controle_controle_examen_show', methods: ['GET'])]
    public function show(ControleExamen $controleExaman): Response
    {
        return $this->render('controle/controle_examen/show.html.twig', [
            'controle_examan' => $controleExaman,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_controle_controle_examen_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ControleExamen $controleExaman, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(ControleExamenType::class, $controleExaman, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_controle_examen_edit', [
                'id' =>  $controleExaman->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_examen_index');




            if ($form->isValid()) {

                $entityManager->persist($controleExaman);
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

        return $this->render('controle/controle_examen/edit.html.twig', [
            'controle_examan' => $controleExaman,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_controle_controle_examen_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, ControleExamen $controleExaman, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_controle_controle_examen_delete',
                    [
                        'id' => $controleExaman->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($controleExaman);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_controle_controle_examen_index');

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

        return $this->render('controle/controle_examen/delete.html.twig', [
            'controle_examan' => $controleExaman,
            'form' => $form,
        ]);
    }
}
