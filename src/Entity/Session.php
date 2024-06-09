<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[UniqueConstraint(name: "numero_session", fields: ["numero", "promotion"])]
#[UniqueEntity(fields: ['numero', 'promotion'], message: 'cette session existe deja pour cette promotion')]
#[Table(name: 'param_session')]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;



    #[ORM\OneToMany(mappedBy: 'session', targetEntity: MoyenneMatiere::class)]
    private Collection $moyenneMatieres;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Promotion $promotion = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: ControleExamen::class)]
    private Collection $controleExamens;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: DecisionExamen::class)]
    private Collection $decisionExamens;

    public function __construct()
    {

        $this->moyenneMatieres = new ArrayCollection();
        $this->controleExamens = new ArrayCollection();
        $this->decisionExamens = new ArrayCollection();
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }


    /**
     * @return Collection<int, MoyenneMatiere>
     */
    /* public function getMoyenneMatieres(): Collection
    {
        return $this->moyenneMatieres;
    }

    public function addMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if (!$this->moyenneMatieres->contains($moyenneMatiere)) {
            $this->moyenneMatieres->add($moyenneMatiere);
            $moyenneMatiere->setSession($this);
        }

        return $this;
    }

    public function removeMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if ($this->moyenneMatieres->removeElement($moyenneMatiere)) {
            
            if ($moyenneMatiere->getSession() === $this) {
                $moyenneMatiere->setSession(null);
            }
        }

        return $this;
    } */

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): static
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * @return Collection<int, ControleExamen>
     */
    public function getControleExamens(): Collection
    {
        return $this->controleExamens;
    }

    public function addControleExamen(ControleExamen $controleExamen): static
    {
        if (!$this->controleExamens->contains($controleExamen)) {
            $this->controleExamens->add($controleExamen);
            $controleExamen->setSession($this);
        }

        return $this;
    }

    public function removeControleExamen(ControleExamen $controleExamen): static
    {
        if ($this->controleExamens->removeElement($controleExamen)) {
            // set the owning side to null (unless already changed)
            if ($controleExamen->getSession() === $this) {
                $controleExamen->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DecisionExamen>
     */
    public function getDecisionExamens(): Collection
    {
        return $this->decisionExamens;
    }

    public function addDecisionExamen(DecisionExamen $decisionExamen): static
    {
        if (!$this->decisionExamens->contains($decisionExamen)) {
            $this->decisionExamens->add($decisionExamen);
            $decisionExamen->setSession($this);
        }

        return $this;
    }

    public function removeDecisionExamen(DecisionExamen $decisionExamen): static
    {
        if ($this->decisionExamens->removeElement($decisionExamen)) {
            // set the owning side to null (unless already changed)
            if ($decisionExamen->getSession() === $this) {
                $decisionExamen->setSession(null);
            }
        }

        return $this;
    }
}
