<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;


    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: Cours::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $cours;

    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: Inscription::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $inscriptions;

    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: Controle::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $controles;

    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: BlocEcheancier::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $blocEcheanciers;

    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Promotion $promotion = null;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->controles = new ArrayCollection();
        $this->blocEcheanciers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setClasse($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getClasse() === $this) {
                $cour->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setClasse($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getClasse() === $this) {
                $inscription->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Controle>
     */
    public function getControles(): Collection
    {
        return $this->controles;
    }

    public function addControle(Controle $controle): static
    {
        if (!$this->controles->contains($controle)) {
            $this->controles->add($controle);
            $controle->setClasse($this);
        }

        return $this;
    }

    public function removeControle(Controle $controle): static
    {
        if ($this->controles->removeElement($controle)) {
            // set the owning side to null (unless already changed)
            if ($controle->getClasse() === $this) {
                $controle->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlocEcheancier>
     */
    public function getBlocEcheanciers(): Collection
    {
        return $this->blocEcheanciers;
    }

    public function addBlocEcheancier(BlocEcheancier $blocEcheancier): static
    {
        if (!$this->blocEcheanciers->contains($blocEcheancier)) {
            $this->blocEcheanciers->add($blocEcheancier);
            $blocEcheancier->setClasse($this);
        }

        return $this;
    }

    public function removeBlocEcheancier(BlocEcheancier $blocEcheancier): static
    {
        if ($this->blocEcheanciers->removeElement($blocEcheancier)) {
            // set the owning side to null (unless already changed)
            if ($blocEcheancier->getClasse() === $this) {
                $blocEcheancier->setClasse(null);
            }
        }

        return $this;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): static
    {
        $this->promotion = $promotion;

        return $this;
    }
}
