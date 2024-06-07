<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: NiveauRepository::class)]
#[Table(name: 'param_niveau')]
#[UniqueConstraint(name: "code_niveau", fields: ["code", "filiere"])]
#[UniqueEntity(fields: ['code', 'filiere'], message: 'Ce code est déjà utilisé pour cette filière')]
class Niveau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'niveaux')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une filière')]
    private ?Filiere $filiere = null;




    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getFullLibelle()
    {
        return sprintf('[%s] %s', $this->getFiliere()->getLibelle(), $this->getLibelle());
    }

    public function getFullLibelleSigle()
    {
        return sprintf('%s %s', $this->getFiliere()->getLibelle(), $this->getCode());
    }
    public function getFullSigle()
    {
        return sprintf('%s ', $this->getCode());
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): static
    {
        $this->filiere = $filiere;

        return $this;
    }


    public function getNom()
    {
        return $this->libelle . ' ' . $this->getFiliere()->getLibelle();
    }
    public function getSigle()
    {
        return $this->code;
    }
}
