<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class EvaluationMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'Evaluation';

    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
        $this->user = $security->getUser();
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setExtra('module', self::MODULE_NAME);
        if ($this->user->hasRoleIn('ROLE_SECRETAIRE') || $this->user->hasRoleIn('ROLE_DIRECTEUR')) {
            $menu->addChild(self::MODULE_NAME, ['label' => 'Evaluations']);
        }

        if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('saisie.index', ['route' => 'app_controle_controle_new_saisie_simple', 'label' => 'Saisie des notes'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('saisieExamen.index', ['route' => 'app_controle_controle_examen_new', 'label' => 'Saisie notes examen'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('session.index', ['route' => 'app_parametre_session_index', 'label' => 'Sessions'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('type.index', ['route' => 'app_controle_type_controle_index', 'label' => 'Type de Controles'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('typeEvaluation.index', ['route' => 'app_controle_type_evaluation_index', 'label' => 'Type évaluation'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            //$menu->addChild('controle.index', ['route' => 'app_controle_controle_index', 'label' => 'Saisie des notes'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('cours.index', ['route' => 'app_controle_cours_index', 'label' => 'Cours'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            // $menu->addChild('deliberation.index', ['route' => 'app_direction_deliberation_index', 'label' => 'GESTION DE EXAMENS'])->setExtra('icon', 'bi bi-gear');
            if ($this->user->getPersonne()->getFonction()->getCode() == "DR") {

                // $menu->addChild('cheque_secretaire', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'Paiements à confirmer'])->setExtra('icon', 'bi bi-cash')->setExtra('role', 'ROLE_COMPTABLE');
                $menu->addChild('parametredirecteur.index', ['route' => 'app_controle_controle_new_saisie_simple', 'label' => 'Saisie des notes'])->setExtra('icon', 'bi bi-book')->setExtra('role', 'ROLE_DIRECTEUR');
            }
        }

        return $menu;
    }
}
