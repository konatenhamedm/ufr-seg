<?php

namespace App\Controller\Parametre;

use App\Attribute\Module;
use App\Attribute\RoleMethod;
use App\Service\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

#[Route('/admin/parametre/dashboard')]
#[Module(name: 'config', excludes: ['liste'])]
class DashboardController extends AbstractController
{

    #[Route(path: '/', name: 'app_parametre_dashboard_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function index(Request $request, Breadcrumb $breadcrumb, UserInterface $user): Response
    {



        // dd($user->getPersonne()->getFonction()->getCode());
        $module = $request->query->get('module');
        if ($user->getPersonne()->getFonction()->getCode() == "DR") {
            $modules = [

                [
                    'label' => 'Gestion',
                    'icon' => 'bi bi-list',
                    'module' => 'gestion',
                    'href' => $this->generateUrl('app_parametre_dashboard_ls', ['module' => 'gestion_directeur'])
                ],
                [
                    'label' => 'Evaluations',
                    'icon' => 'bi bi-list',
                    'module' => 'general',
                    'href' => $this->generateUrl('app_parametre_dashboard_ls', ['module' => 'evaluation'])
                ],
            ];
        } else {
            $modules = [
                [
                    'label' => 'Général',
                    'icon' => 'bi bi-list',
                    'module' => 'general',
                    'href' => $this->generateUrl('app_parametre_dashboard_ls', ['module' => 'general'])
                ],
                [
                    'label' => 'Gestion',
                    'icon' => 'bi bi-list',
                    'module' => 'gestion',
                    'href' => $this->generateUrl('app_parametre_dashboard_ls', ['module' => 'gestion'])
                ],
            ];
        }

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('parametre/dashboard/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }


    #[Route(path: '/{module}', name: 'app_parametre_dashboard_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [
            'general' => [
                [
                    'label' => 'Fonctions',
                    'id' => 'param_fonction',
                    'href' => $this->generateUrl('app_parametre_fonction_index')
                ],
                [
                    'label' => 'Civilités',
                    'id' => 'param_civilite',
                    'href' => $this->generateUrl('app_parametre_civilite_index')
                ],
                [
                    'label' => 'Genres',
                    'id' => 'param_genre',
                    'href' => $this->generateUrl('app_parametre_genre_index')
                ],
            ],
            'gestion' => [
                [
                    'label' => 'Années scolaire',
                    'id' => 'param_as',
                    'href' => $this->generateUrl('app_parametre_annee_scolaire_index')
                ],
                [
                    'label' => 'Semestres',
                    'id' => 'param_sem',
                    'href' => $this->generateUrl('app_parametre_semestre_index')
                ],
                [
                    'label' => 'Types de frais',
                    'id' => 'param_type_frais',
                    'href' => $this->generateUrl('app_parametre_type_frais_index')
                ],
                [
                    'label' => 'Nature des paiements',
                    'id' => 'param_nature_paiement',
                    'href' => $this->generateUrl('app_parametre_nature_paiement_index')
                ],
                [
                    'label' => 'UFR',
                    'id' => 'param_ufr',
                    'href' => $this->generateUrl('app_parametre_unite_formation_index')
                ],
                [
                    'label' => 'Filières',
                    'id' => 'param_filiere',
                    'href' => $this->generateUrl('app_parametre_filiere_index')
                ],
                [
                    'label' => 'Niveaux',
                    'id' => 'param_niveau',
                    'href' => $this->generateUrl('app_parametre_niveau_index')
                ],
                [
                    'label' => 'Classes',
                    'id' => 'param_classe',
                    'href' => $this->generateUrl('app_parametre_classe_index')
                ],
                [
                    'label' => 'Type de matière',
                    'id' => 'param_type_matiere',
                    'href' => $this->generateUrl('app_parametre_type_matiere_index')
                ],
                [
                    'label' => 'Matières',
                    'id' => 'param_matiere',
                    'href' => $this->generateUrl('app_parametre_matiere_index')
                ],

                /*  [
                    'label' => 'Enseignants par matière',
                    'id' => 'param_em',
                    'href' => $this->generateUrl('app_parametre_cours_index')
                ], */
                [
                    'label' => "Unité d'enseigenement",
                    'id' => 'param_ue',
                    'href' => $this->generateUrl('app_parametre_unite_enseignement_index')
                ],
                [
                    'label' => 'Mentions',
                    'id' => 'param_mention',
                    'href' => $this->generateUrl('app_parametre_mention_index')
                ],


            ],
            'gestion_directeur' => [

                [
                    'label' => 'Filières',
                    'id' => 'param_filiere',
                    'href' => $this->generateUrl('app_parametre_filiere_index')
                ],
                [
                    'label' => 'Niveaux',
                    'id' => 'param_niveau',
                    'href' => $this->generateUrl('app_parametre_niveau_index')
                ],
                [
                    'label' => 'Classes',
                    'id' => 'param_classe',
                    'href' => $this->generateUrl('app_parametre_classe_index')
                ],
                [
                    'label' => 'Type de matière',
                    'id' => 'param_type_matiere',
                    'href' => $this->generateUrl('app_parametre_type_matiere_index')
                ],
                [
                    'label' => 'Matières',
                    'id' => 'param_matiere',
                    'href' => $this->generateUrl('app_parametre_matiere_ue_index')
                ],
                [
                    'label' => "Unité d'enseigenement",
                    'id' => 'param_ue',
                    'href' => $this->generateUrl('app_parametre_unite_enseignement_index')
                ]


            ],

            'evaluation' => [
                [
                    'label' => 'Type de Controles',
                    'id' => 'param_evaluation_type',
                    'href' => $this->generateUrl('app_controle_type_controle_index')
                ],

                [
                    'label' => 'Sessions',
                    'id' => 'param_evaluation_session',
                    'href' => $this->generateUrl('app_parametre_session_index')
                ],
                [
                    'label' => 'Cours',
                    'id' => 'param_evaluation_cour',
                    'href' => $this->generateUrl('app_controle_cours_index')
                ],

            ]
        ];


        return $this->render('parametre/dashboard/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}
