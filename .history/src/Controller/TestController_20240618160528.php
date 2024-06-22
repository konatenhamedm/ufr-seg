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
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render(
            'test/index.html.twig',
        );
    }

    #[Route('/test', name: 'app_test', methods: ['GET', 'POST'])]
    public function imprimerAll(Request $request, $niveau, $caissiere, $dateDebut, $dateFin, $mode): Response
    {

        // $niveau = $request->query->get('niveau');


        $totalImpaye = 0;
        $totalPayer = 0;


        // dd($_SESSION['token']);

        //$id = intval($request->query->get('niveau'));
        //$dateNiveau = $niveauRepository->find(intval($niveau))->getLibelle();
        /* if ($niveau) {
                 = $niveauRepository->find(intval($niveau));
            } else {
                $dateNiveau = null;
            } */



     
        //dd($dateNiveau);
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("site/liste.html.twig", [
            'total_payer' => $totalPayer,
            'data' => ,
            'total_imp5aye' => $totalImpaye
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
