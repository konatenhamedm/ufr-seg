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
/* #[UniqueConstraint(name: "code_niveau", fields: ["code", "filiere"])]
#[UniqueEntity(fields: ['code', 'filiere'], message: 'Ce code est déjà utilisé pour cette filière')] */
class Niveau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'niveaux')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une filière')]
    private ?Filiere $filiere = null;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Frais::class, orphanRemoval: true, cascade: ['persist'])]
    #[Assert\Valid(groups: ['niveau-frais'])]
    private Collection $frais;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un responsable')]
    private ?Employe $responsable = null;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: InfoNiveau::class, cascade: ['persist'])]
    private Collection $infoNiveaux;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: UniteEnseignement::class)]
    private Collection $uniteEnseignements;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Classe::class)]
    private Collection $classes;

    #[ORM\ManyToOne(inversedBy: 'niveaux')]
    private ?Promotion $promotion = null;

    #[ORM\ManyToOne(inversedBy: 'niveaux')]
    private ?AnneeScolaire $anneeScolaire = null;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: DecisionExamen::class)]
    private Collection $decisionExamens;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: ControleExamen::class)]
    private Collection $controleExamens;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Examen::class)]
    private Collection $examens;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Session::class)]
    private Collection $sessions;




    public function __construct()
    {
        $this->frais = new ArrayCollection();
        $this->infoNiveaux = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->uniteEnseignements = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->decisionExamens = new ArrayCollection();
        $this->controleExamens = new ArrayCollection();
        $this->examens = new ArrayCollection();
        $this->sessions = new ArrayCollection();
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
    public function getFullCodeAnneeScolaire()
    {
        return sprintf('[%s] %s %s', $this->getCode(), '-', $this->getAnneeScolaire()->getLibelle());
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

    /**
     * @return Collection<int, Frais>
     */
    public function getFrais(): Collection
    {
        return $this->frais;
    }

    public function addFrai(Frais $frai): static
    {
        if (!$this->frais->contains($frai)) {
            $this->frais->add($frai);
            $frai->setNiveau($this);
        }

        return $this;
    }

    public function removeFrai(Frais $frai): static
    {
        if ($this->frais->removeElement($frai)) {
            // set the owning side to null (unless already changed)
            if ($frai->getNiveau() === $this) {
                $frai->setNiveau(null);
            }
        }

        return $this;
    }

    public function getResponsable(): ?Employe
    {
        return $this->responsable;
    }

    public function setResponsable(?Employe $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * @return Collection<int, InfoNiveau>
     */
    public function getInfoNiveaux(): Collection
    {
        return $this->infoNiveaux;
    }

    public function addInfoNiveau(InfoNiveau $infoNiveau): static
    {
        if (!$this->infoNiveaux->contains($infoNiveau)) {
            $this->infoNiveaux->add($infoNiveau);
            $infoNiveau->setNiveau($this);
        }

        return $this;
    }

    public function removeInfoNiveau(InfoNiveau $infoNiveau): static
    {
        if ($this->infoNiveaux->removeElement($infoNiveau)) {
            // set the owning side to null (unless already changed)
            if ($infoNiveau->getNiveau() === $this) {
                $infoNiveau->setNiveau(null);
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


    public function getNom()
    {
        return $this->libelle . ' ' . $this->getFiliere()->getLibelle();
    }
    public function getSigle()
    {
        return $this->code;
    }

    /**
     * @return Collection<int, UniteEnseignement>
     */
    public function getUniteEnseignements(): Collection
    {
        return $this->uniteEnseignements;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): static
    {
        if (!$this->uniteEnseignements->contains($uniteEnseignement)) {
            $this->uniteEnseignements->add($uniteEnseignement);
            $uniteEnseignement->setNiveau($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): static
    {
        if ($this->uniteEnseignements->removeElement($uniteEnseignement)) {
            // set the owning side to null (unless already changed)
            if ($uniteEnseignement->getNiveau() === $this) {
                $uniteEnseignement->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setNiveau($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getNiveau() === $this) {
                $class->setNiveau(null);
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

    public function getAnneeScolaire(): ?AnneeScolaire
    {
        return $this->anneeScolaire;
    }

    public function setAnneeScolaire(?AnneeScolaire $anneeScolaire): static
    {
        $this->anneeScolaire = $anneeScolaire;

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
            $decisionExamen->setNiveau($this);
        }

        return $this;
    }

    public function removeDecisionExamen(DecisionExamen $decisionExamen): static
    {
        if ($this->decisionExamens->removeElement($decisionExamen)) {
            // set the owning side to null (unless already changed)
            if ($decisionExamen->getNiveau() === $this) {
                $decisionExamen->setNiveau(null);
            }
        }

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
            $controleExamen->setNiveau($this);
        }

        return $this;
    }

    public function removeControleExamen(ControleExamen $controleExamen): static
    {
        if ($this->controleExamens->removeElement($controleExamen)) {
            // set the owning side to null (unless already changed)
            if ($controleExamen->getNiveau() === $this) {
                $controleExamen->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Examen>
     */
    public function getExamens(): Collection
    {
        return $this->examens;
    }

    public function addExamen(Examen $examen): static
    {
        if (!$this->examens->contains($examen)) {
            $this->examens->add($examen);
            $examen->setNiveau($this);
        }

        return $this;
    }

    public function removeExamen(Examen $examen): static
    {
        if ($this->examens->removeElement($examen)) {
            // set the owning side to null (unless already changed)
            if ($examen->getNiveau() === $this) {
                $examen->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setNiveau($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getNiveau() === $this) {
                $session->setNiveau(null);
            }
        }

        return $this;
    }
}
