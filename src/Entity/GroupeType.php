<?php

namespace App\Entity;

use App\Repository\GroupeTypeRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: GroupeTypeRepository::class)]
#[Table(name: 'evaluation_groupe_type')]
class GroupeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNote = null;


    #[ORM\ManyToOne(inversedBy: 'groupeTypes')]
    private ?Controle $controle = null;

    #[ORM\ManyToOne(inversedBy: 'groupeTypes')]
    private ?TypeEvaluation $typeEvaluation = null;

    #[ORM\Column]
    private ?int $coef = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSaisie = null;


    public function __construct()
    {
        $this->dateSaisie = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateNote(): ?\DateTimeInterface
    {
        return $this->dateNote;
    }

    public function setDateNote(?\DateTimeInterface $dateNote): static
    {
        $this->dateNote = $dateNote;

        return $this;
    }

    public function getControle(): ?Controle
    {
        return $this->controle;
    }

    public function setControle(?Controle $controle): static
    {
        $this->controle = $controle;

        return $this;
    }

    public function getTypeEvaluation(): ?TypeEvaluation
    {
        return $this->typeEvaluation;
    }

    public function setTypeEvaluation(?TypeEvaluation $typeEvaluation): static
    {
        $this->typeEvaluation = $typeEvaluation;

        return $this;
    }

    public function getCoef(): ?int
    {
        return $this->coef;
    }

    public function setCoef(int $coef): static
    {
        $this->coef = $coef;

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
}
