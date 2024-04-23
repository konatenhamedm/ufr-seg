<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;

class HomeMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'home';

    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->user = $security->getUser();
    }


    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        // $a = false;
        if (!in_array("ROLE_ETUDIANT", $this->user->getRoles())) {
            //dd(!in_array("ROLE_ETUDIANT", $this->user->getRoles()));
            $menu->addChild('dashboard', ['route' => 'app_liste_inscription_etudiant_admin_index', 'label' => 'Tableau de bord'])
                ->setExtra('icon', 'bi bi-house')
                ->setExtra('role', 'ROLE_ALL');
        }



        return $menu;
    }
}
