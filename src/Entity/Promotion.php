<?php

namespace App\Entity;

use App\Repository\PromotionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]
#[Table(name: 'param_promotion')]
#[UniqueEntity(fields: 'code', message: 'Ce code est déjà utilisé')]
#[UniqueConstraint(name: "numero_niveau", fields: ["numero", "niveau"])]
#[UniqueEntity(fields: ['numero', 'niveau'], message: 'cette promotion existe deja')]
class Promotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'promotions')]
    private ?Niveau $niveau = null;


    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'promotions')]
    private ?AnneeScolaire $anneeScolaire = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: Preinscription::class)]
    private Collection $preinscriptions;



    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: Session::class)]
    private Collection $sessions;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: Classe::class)]
    private Collection $classes;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un responsable')]
    private ?Employe $responsable = null;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: Frais::class, orphanRemoval: true, cascade: ['persist'])]
    #[Assert\Valid(groups: ['promotion-frais'])]
    private Collection $frais;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: UniteEnseignement::class)]
    private Collection $uniteEnseignements;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: InfoNiveau::class, cascade: ['persist'])]
    private Collection $infoNiveaux;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: ControleExamen::class)]
    private Collection $controleExamens;

    public function __construct()
    {
        $this->infoNiveaux = new ArrayCollection();
        $this->preinscriptions = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->frais = new ArrayCollection();
        $this->uniteEnseignements = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->controleExamens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFullSigle()
    {

        return sprintf('%s %s', $this->getNiveau()->getFullSigle(), $this->getNumero());
    }
    public function getFullSigleNiveau()
    {

        return $this->getNiveau()->getFullSigle();
    }

    public function getFullLibelleSigle()
    {
        return sprintf('%s %s', $this->getNiveau()->getFiliere()->getLibelle(), $this->getNiveau()->getCode());
    }

    public function fullLibelle()
    {
        return $this->getNiveau()->getFullLibelle();
    }
    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
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

    public function getAnneeScolaire(): ?AnneeScolaire
    {
        return $this->anneeScolaire;
    }

    public function setAnneeScolaire(?AnneeScolaire $anneeScolaire): static
    {
        $this->anneeScolaire = $anneeScolaire;

        return $this;
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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return Collection<int, Preinscription>
     */
    public function getPreinscriptions(): Collection
    {
        return $this->preinscriptions;
    }

    public function addPreinscription(Preinscription $preinscription): static
    {
        if (!$this->preinscriptions->contains($preinscription)) {
            $this->preinscriptions->add($preinscription);
            $preinscription->setPromotion($this);
        }

        return $this;
    }

    public function removePreinscription(Preinscription $preinscription): static
    {
        if ($this->preinscriptions->removeElement($preinscription)) {
            // set the owning side to null (unless already changed)
            if ($preinscription->getPromotion() === $this) {
                $preinscription->setPromotion(null);
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
            $session->setPromotion($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getPromotion() === $this) {
                $session->setPromotion(null);
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
            $class->setPromotion($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getPromotion() === $this) {
                $class->setPromotion(null);
            }
        }

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
            $frai->setPromotion($this);
        }

        return $this;
    }

    public function removeFrai(Frais $frai): static
    {
        if ($this->frais->removeElement($frai)) {
            // set the owning side to null (unless already changed)
            if ($frai->getPromotion() === $this) {
                $frai->setPromotion(null);
            }
        }

        return $this;
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
            $uniteEnseignement->setPromotion($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): static
    {
        if ($this->uniteEnseignements->removeElement($uniteEnseignement)) {
            // set the owning side to null (unless already changed)
            if ($uniteEnseignement->getPromotion() === $this) {
                $uniteEnseignement->setPromotion(null);
            }
        }

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
            $infoNiveau->setPromotion($this);
        }

        return $this;
    }

    public function removeInfoNiveau(InfoNiveau $infoNiveau): static
    {
        if ($this->infoNiveaux->removeElement($infoNiveau)) {
            // set the owning side to null (unless already changed)
            if ($infoNiveau->getPromotion() === $this) {
                $infoNiveau->setPromotion(null);
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
            $inscription->setPromotion($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getPromotion() === $this) {
                $inscription->setPromotion(null);
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
            $controleExamen->setPromotion($this);
        }

        return $this;
    }

    public function removeControleExamen(ControleExamen $controleExamen): static
    {
        if ($this->controleExamens->removeElement($controleExamen)) {
            // set the owning side to null (unless already changed)
            if ($controleExamen->getPromotion() === $this) {
                $controleExamen->setPromotion(null);
            }
        }

        return $this;
    }
}
