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


    #[Route('/test', name: 'app_test', methods: ['GET', 'POST'])]
    public function imprimerAll(Request $request): Response
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
}
