<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class TestController extends AbstractController
{
    use FileTrait;
    /*  'first_print' => [
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
    ], */

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
    public function imprimerAll2(Request $request): Response
    {

        $totalImpaye = 0;
        $totalPayer = 0;

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
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
}
