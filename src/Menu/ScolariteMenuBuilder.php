<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class ScolariteMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'Scolarite';

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
        if ($this->user->hasRoleIn('ROLE_ADMIN') || $this->user->hasRoleIn('ROLE_DIRECTEUR')) {
            $menu->addChild(self::MODULE_NAME, ['label' => 'Scolarite']);
        }

        if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('scolarite.index', ['route' => 'app_home_timeline_index', 'label' => ' Gestion des dossiers'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('inscription', ['route' => 'app_inscription_etudiant_admin_index', 'label' => ' Inscription'])->setExtra('icon', 'bi bi-gear');
            $menu->addChild('reinscription.index', ['route' => 'app_reinscription_etudiant_admin_index', 'label' => ' Réinscription'])->setExtra('icon', 'bi bi-gear');
            $menu->addChild('etat_classe', ['route' => 'app_parametre_classe_imprime_index', 'label' => ' Etats classes'])->setExtra('icon', 'bi bi-gear');
            $menu->addChild('etat_etudiants', ['route' => 'app_inscription_etudiant_etats_index', 'label' => ' Etats étudiants'])->setExtra('icon', 'bi bi-gear');
            $menu->addChild('etat_bulletin', ['route' => 'app_inscription_edition_bulletin_etats_index', 'label' => ' Edition bulletins'])->setExtra('icon', 'bi bi-gear');
            if ($this->user->getPersonne()->getFonction()->getCode() == "DR") {

                // $menu->addChild('cheque_secretaire', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'Paiements à confirmer'])->setExtra('icon', 'bi bi-cash')->setExtra('role', 'ROLE_COMPTABLE');
                $menu->addChild('scolaritedirecteur.index', ['route' => 'app_home_timeline_index', 'label' => ' Gestion des dossiers'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_DIRECTEUR');
                $menu->addChild('inscriptiondirecteur', ['route' => 'app_inscription_etudiant_admin_index', 'label' => ' Inscription'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_DIRECTEUR');
            }

            // $menu->addChild('inscription', ['route' => 'app_inscription_etudiant_admin_index', 'label' => ' Inscription'])->setExtra('icon', 'bi bi-gear');
        }

        return $menu;
    }
}
