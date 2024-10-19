<?php

namespace App\Controller\Test;

use App\Controller\FileTrait;
use App\Entity\Test;
use App\Form\TestType;
use App\Repository\ClasseRepository;
use App\Repository\TestRepository;
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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

#[Route('/admin/test/test')]
class TestController extends AbstractController
{
    use FileTrait;

    protected const UPLOAD_PATH = 'media_entreprise';

    #[Route('/', name: 'app_test_test_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->createAdapter(ORMAdapter::class, [
                'entity' => Test::class,
            ])
            ->setName('dt_app_test_test');

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
                'render' => function ($value, Test $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_test_test_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_test_test_delete', ['id' => $value]),
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


        return $this->render('test/test/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_test_test_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $test = new Test();
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(TestType::class, $test, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_test_test_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_test_test_index');




            if ($form->isValid()) {

                $entityManager->persist($test);
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

        return $this->render('test/test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_test_test_show', methods: ['GET'])]
    public function show(Test $test): Response
    {
        return $this->render('test/test/show.html.twig', [
            'test' => $test,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_test_test_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Test $test, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(TestType::class, $test, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_test_test_edit', [
                'id' =>  $test->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_test_test_index');




            if ($form->isValid()) {

                $entityManager->persist($test);
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

        return $this->render('test/test/edit.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_test_test_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Test $test, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_test_test_delete',
                    [
                        'id' => $test->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($test);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_test_test_index');

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

        return $this->render('test/test/delete.html.twig', [
            'test' => $test,
            'form' => $form,
        ]);
    }



    #[Route('/test', name: 'app_test', methods: ['GET', 'POST'])]
    public function imprimerAll(Request $request): Response
    {

        $totalImpaye = 0;
        $totalPayer = 0;

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("test/liste_presence.html.twig", [
            'total_payer' => $totalPayer,
            'data' => [],
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
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

    #[Route('/attesta', name: 'app_test', methods: ['GET', 'POST'])]
    public function imprimerAttesta(Request $request): Response
    {

        $totalImpaye = 0;
        $totalPayer = 0;

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("test/attestation.html.twig", [
            'total_payer' => $totalPayer,
            'data' => [],
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
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



    #[Route('/certif', name: 'app_test', methods: ['GET', 'POST'])]
    public function imprimercertif(Request $request): Response
    {

        $totalImpaye = 0;
        $totalPayer = 0;

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("test/certification.html.twig", [
            'total_payer' => $totalPayer,
            'data' => [],
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
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
    #[Route('/test4', name: 'app_test4', methods: ['GET', 'POST'])]
    public function imprimerAll4(Request $request): Response
    {

        $totalImpaye = 0;
        $totalPayer = 0;

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("test/index.html.twig", [
            'total_payer' => $totalPayer,
            'data' => [],
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
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
    #[Route('/test1', name: 'app_test1', methods: ['GET', 'POST'])]
    public function imprimerAll1(Request $request): Response
    {

        $totalImpaye = 0;
        $totalPayer = 0;

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("test/fiche_de_note.html.twig", [
            'total_payer' => $totalPayer,
            'data' => [],
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
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

    #[Route('/test2', name: 'app_test2', methods: ['GET', 'POST'])]
    public function imprimerAll21(Request $request,  ): Response
    {
        //  $array = ;



        $data = [];
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
      //  $tableaus = json_decode($array_final, true);

       // $longeur = count($tableaus);

        // dd($longeur);
        // for ($i = 0; $i < $longeur; $i++) {
        //     $data[] = $classeRepository->find($tableaus[$i]);
        // }



        $totalImpaye = 0;
        $totalPayer = 0;

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        // return $this->renderPdf("test/liste_de_class.html.twig", [
        //     'total_payer' => $totalPayer,
        //     'data' => $data,
        //     'total_impaye' => $totalImpaye,
        //     'anneeScolaire' =>  $anneeScolaire = $session->get('anneeScolaire')->getLibelle()
        //     //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        // ], [
        //     'orientation' => 'P',
        //     'protected' => true,
        //     'file_name' => "point_versments",

        //     'format' => 'A4',

        //     'showWaterkText' => true,
        //     'fontDir' => [
        //         $this->getParameter('font_dir') . '/arial',
        //         $this->getParameter('font_dir') . '/trebuchet',
        //     ],
        //     'watermarkImg' => $imgFiligrame,
        //     'entreprise' => ''
        // ], true);

        return $this->renderPdf("test/liste_de_class.html.twig", [
            'total_payer' => $totalPayer,
            'data' => [],
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
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

    #[Route('/excel', name: 'app_excel', methods: ['GET', 'POST'])]
    public function exportExcel(): Response
    {
        // Crée un nouvel objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Définir les en-têtes de colonne avec style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F81BD'], // Couleur de fond
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ];

        // Définir les cellules de l'en-tête
        $sheet->setCellValue('A1', 'Identifiant Permanent (IP)');
        $sheet->setCellValue('B1', 'Matricule BAC');
        $sheet->setCellValue('C1', 'Nom');
        $sheet->setCellValue('D1', 'Prenoms');
        $sheet->setCellValue('E1', 'Date de Naissance');
        $sheet->setCellValue('F1', 'Lieu de naissance');
        $sheet->setCellValue('G1', 'Adresse');
        $sheet->setCellValue('H1', 'Sexe');
        $sheet->setCellValue('I1', 'Nationalité');
        $sheet->setCellValue('J1', 'Etablissement antérieur');
        $sheet->setCellValue('K1', 'Diplôme équivalence (Diplôme Presenté)');
        $sheet->setCellValue('L1', 'Année d\'obtention du Diplôme');
        $sheet->setCellValue('M1', 'Mention (accordée)');
        

        // Fusionner les cellules pour une en-tête plus large (optionnel)
        // $sheet->mergeCells('A1:C1');

        // Appliquer le style à l'en-tête
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Ajuster la taille des colonnes
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        // Ajouter des données (exemple statique ici, remplacez avec vos données)
        $data = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['name']);
            $sheet->setCellValue('C' . $row, $item['email']);
            $row++;
        }

        // Préparer la réponse HTTP pour le téléchargement
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="export.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
    // #[Route('/test2/{etat}/{classe}', name: 'app_test2', methods: ['GET', 'POST'])]
    // public function imprimerAll2(Request $request,  $classe,  $etat, ClasseRepository $classeRepository, SessionInterface $session): Response
    // {
    //     //  $array = ;



    //     $data = [];
    //     $array_final = '[' . implode(',', explode(',', $etat)) . ']';
    //     $tableaus = json_decode($array_final, true);

    //     $longeur = count($tableaus);

    //     // dd($longeur);
    //     for ($i = 0; $i < $longeur; $i++) {
    //         $data[] = $classeRepository->find($tableaus[$i]);
    //     }



    //     $totalImpaye = 0;
    //     $totalPayer = 0;

    //     $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
    //     return $this->renderPdf("test/liste_de_class.html.twig", [
    //         'total_payer' => $totalPayer,
    //         'data' => $data,
    //         'total_impaye' => $totalImpaye,
    //         'anneeScolaire' =>  $anneeScolaire = $session->get('anneeScolaire')->getLibelle()
    //         //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
    //     ], [
    //         'orientation' => 'P',
    //         'protected' => true,
    //         'file_name' => "point_versments",

    //         'format' => 'A4',

    //         'showWaterkText' => true,
    //         'fontDir' => [
    //             $this->getParameter('font_dir') . '/arial',
    //             $this->getParameter('font_dir') . '/trebuchet',
    //         ],
    //         'watermarkImg' => $imgFiligrame,
    //         'entreprise' => ''
    //     ], true);
    //     //return $this->renderForm("stock/sortie/imprime.html.twig");

    // }
}
