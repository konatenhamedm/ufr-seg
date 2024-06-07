<?php

namespace App\Entity;

use App\Repository\GroupeTypeExamenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;


#[ORM\Entity(repositoryClass: GroupeTypeExamenRepository::class)]
#[Table(name: 'evaluation_examen_groupe_type')]
class GroupeTypeExamen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCompo = null;

    #[ORM\Column]
    private ?int $max = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSaisie = null;

    #[ORM\ManyToOne(inversedBy: 'groupeTypeExamens')]
    private ?ControleExamen $controleExamen = null;

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;


    public function __construct()
    {
        $this->dateSaisie = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCompo(): ?\DateTimeInterface
    {
        return $this->dateCompo;
    }

    public function setDateCompo(\DateTimeInterface $dateCompo): static
    {
        $this->dateCompo = $dateCompo;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getDateSaisie(): ?\DateTimeInterface
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTimeInterface $dateSaisie): static
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }

    public function getControleExamen(): ?ControleExamen
    {
        return $this->controleExamen;
    }

    public function setControleExamen(?ControleExamen $controleExamen): static
    {
        $this->controleExamen = $controleExamen;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
