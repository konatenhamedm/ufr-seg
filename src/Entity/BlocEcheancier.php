<?php

namespace App\Entity;

use App\Repository\BlocEcheancierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlocEcheancierRepository::class)]
class BlocEcheancier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blocEcheanciers')]
    private ?Classe $classe = null;



    #[ORM\Column(length: 255)]
    private ?string $total = null;

    #[ORM\ManyToOne(inversedBy: 'blocEcheanciers')]
    private ?Etudiant $etudiant = null;

    #[ORM\OneToMany(mappedBy: 'blocEcheancier', targetEntity: EcheancierProvisoire::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $echeancierProvisoires;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\ManyToOne(inversedBy: 'blocEcheanciers')]
    private ?Inscription $inscription = null;

    #[ORM\OneToMany(mappedBy: 'blocEcheancier', targetEntity: FraisBloc::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $fraisBlocs;



    public function __construct()
    {
        $this->echeancierProvisoires = new ArrayCollection();
        $this->fraisBlocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

        return $this;
    }


    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    /**
     * @return Collection<int, EcheancierProvisoire>
     */
    public function getEcheancierProvisoires(): Collection
    {
        return $this->echeancierProvisoires;
    }

    public function addEcheancierProvisoire(EcheancierProvisoire $echeancierProvisoire): static
    {
        if (!$this->echeancierProvisoires->contains($echeancierProvisoire)) {
            $this->echeancierProvisoires->add($echeancierProvisoire);
            $echeancierProvisoire->setBlocEcheancier($this);
        }

        return $this;
    }

    public function removeEcheancierProvisoire(EcheancierProvisoire $echeancierProvisoire): static
    {
        if ($this->echeancierProvisoires->removeElement($echeancierProvisoire)) {
            // set the owning side to null (unless already changed)
            if ($echeancierProvisoire->getBlocEcheancier() === $this) {
                $echeancierProvisoire->setBlocEcheancier(null);
            }
        }

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription($dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): static
    {
        $this->inscription = $inscription;

        return $this;
    }

    /**
     * @return Collection<int, FraisBloc>
     */
    public function getFraisBlocs(): Collection
    {
        return $this->fraisBlocs;
    }

    public function addFraisBloc(FraisBloc $fraisBloc): static
    {
        if (!$this->fraisBlocs->contains($fraisBloc)) {
            $this->fraisBlocs->add($fraisBloc);
            $fraisBloc->setBlocEcheancier($this);
        }

        return $this;
    }

    public function removeFraisBloc(FraisBloc $fraisBloc): static
    {
        if ($this->fraisBlocs->removeElement($fraisBloc)) {
            // set the owning side to null (unless already changed)
            if ($fraisBloc->getBlocEcheancier() === $this) {
                $fraisBloc->setBlocEcheancier(null);
            }
        }

        return $this;
    }
}
