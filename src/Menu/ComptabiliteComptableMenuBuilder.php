<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class ComptabiliteComptableMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'admin';

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
        if ($this->user->hasRole("ROLE_COMPTABLE") || $this->user->hasRole('ROLE_DIRECTEUR')) {
            $menu->addChild(self::MODULE_NAME, ['label' => 'Comptabilité']);
        }



        if (isset($menu[self::MODULE_NAME])) {

            $menu->addChild('parametre.index', ['route' => 'app_parametre_preinscription_index', 'label' => 'Préinscriptions'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_COMPTABLE');
            // $menu->addChild('personne.index', ['route' => 'app_config_frais_gestion_index', 'label' => 'Frais de scolarité'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_COMPTABLE');
            $menu->addChild('personne.index', ['route' => 'app_inscription_inscription_frais_index', 'label' => 'Frais de scolarité non soldés'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_CAISSIERE');
            $menu->addChild('solde', ['route' => 'app_inscription_inscription_frais_solde_index', 'label' => 'Frais de scolarité soldés'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_CAISSIERE');
            $menu->addChild('personne.index', ['route' => 'app_inscription_inscription_frais_index', 'label' => 'Frais de scolarité non soldés'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_COMPTABLE');
            $menu->addChild('solde', ['route' => 'app_inscription_inscription_frais_solde_index', 'label' => 'Frais de scolarité soldés'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_COMPTABLE');

            $menu->addChild('paiement', ['route' => 'app_config_preinscription_point_paiement_index', 'label' => 'Point des paiements'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_COMPTABLE');
            $menu->addChild('cheque', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'Paiements à confirmer'])->setExtra('icon', 'bi bi-cash')->setExtra('role', 'ROLE_COMPTABLE');
            if (!$this->user->hasRoleIn("ROLE_COMPTABLE")) {

                $menu->addChild('cheque_secretaire', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'Paiements à confirmer'])->setExtra('icon', 'bi bi-cash')->setExtra('role', 'ROLE_COMPTABLE');
            }

            if ($this->user->getPersonne()->getFonction()->getCode() == "DR") {
                $menu->addChild('paiement', ['route' => 'app_config_preinscription_point_paiement_index', 'label' => 'Point des paiements'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_DIRECTEUR');

                //$menu->addChild('examen.index', ['route' => 'app_direction_examen_index', 'label' => 'Gestion des examens'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_DIRECTEUR');
            }
        }

        return $menu;
    }
}
