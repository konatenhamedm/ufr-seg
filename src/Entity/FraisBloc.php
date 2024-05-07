<?php

namespace App\Entity;

use App\Repository\FraisBlocRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FraisBlocRepository::class)]
class FraisBloc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fraisBlocs')]
    private ?TypeFrais $typeFrais = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'fraisBlocs')]
    private ?BlocEcheancier $blocEcheancier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeFrais(): ?TypeFrais
    {
        return $this->typeFrais;
    }

    public function setTypeFrais(?TypeFrais $typeFrais): static
    {
        $this->typeFrais = $typeFrais;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getBlocEcheancier(): ?BlocEcheancier
    {
        return $this->blocEcheancier;
    }

    public function setBlocEcheancier(?BlocEcheancier $blocEcheancier): static
    {
        $this->blocEcheancier = $blocEcheancier;

        return $this;
    }
}
