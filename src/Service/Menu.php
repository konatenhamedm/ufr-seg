<?php

namespace App\Service;

use App\Entity\InfoInscription;
use App\Repository\AnneeScolaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

use Psr\Container\ContainerInterface;
use function PHPUnit\Framework\isEmpty;

class Menu
{

    private $em;
    private $route;
    private $container;
    private $security;

    private $resp;
    private $tableau = [];
    private $anneeScolaireRepository;


    public function __construct(EntityManagerInterface $em, AnneeScolaireRepository $anneeScolaireRepository, RequestStack $requestStack, RouterInterface $router, Security $security)
    {
        $this->em = $em;
        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
            $this->security = $security;
        }
        $this->anneeScolaireRepository = $anneeScolaireRepository;
    }
    public function getRoute()
    {
        return $this->route;
    }
    public function getListeAnnees()
    {
        return $this->anneeScolaireRepository->findAll();
    }

    public function getSomme($etudiant, $year, $niveau)
    {
        $data = $this->em->getRepository(InfoInscription::class)->rangeSommeParAnnneNiveauEtudiant($etudiant, $year, $niveau);
        return $data ? $data['somme'] : '';
    }
    public function getSommeTotal($etudiant, $niveau)
    {
        $data = $this->em->getRepository(InfoInscription::class)->sommeTotal($etudiant, $niveau);
        return $data ? $data['somme'] : 0;
    }
}
