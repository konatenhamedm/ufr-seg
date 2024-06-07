<?php

namespace App\Entity;

use App\Repository\UniteEnseignementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UniteEnseignementRepository::class)]
class UniteEnseignement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'uniteEnseignements')]
    private ?Semestre $semestre = null;

    #[ORM\Column(length: 255)]
    private ?string $codeUe = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $coef = null;

    #[ORM\Column(length: 255)]
    private ?string $attribut = null;

    #[ORM\Column]
    private ?int $volumeHoraire = null;

    #[ORM\Column]
    private ?int $totalCredit = null;



    #[ORM\OneToMany(mappedBy: 'uniteEnseignement', targetEntity: MatiereUe::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $matiereUes;

    #[ORM\ManyToOne(inversedBy: 'uniteEnseignements')]
    private ?Promotion $promotion = null;

    #[ORM\OneToMany(mappedBy: 'ue', targetEntity: Controle::class)]
    private Collection $controles;

    #[ORM\OneToMany(mappedBy: 'ue', targetEntity: MoyenneMatiere::class)]
    private Collection $moyenneMatieres;

    #[ORM\OneToMany(mappedBy: 'ue', targetEntity: ControleExamen::class)]
    private Collection $controleExamens;

    public function __construct()
    {
        $this->matiereUes = new ArrayCollection();
        $this->controles = new ArrayCollection();
        $this->moyenneMatieres = new ArrayCollection();
        $this->controleExamens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): static
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getCodeUe(): ?string
    {
        return $this->codeUe;
    }

    public function setCodeUe(string $codeUe): static
    {
        $this->codeUe = $codeUe;

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

    public function getCoef(): ?string
    {
        return $this->coef;
    }

    public function setCoef(string $coef): static
    {
        $this->coef = $coef;

        return $this;
    }

    public function getAttribut(): ?string
    {
        return $this->attribut;
    }

    public function setAttribut(string $attribut): static
    {
        $this->attribut = $attribut;

        return $this;
    }

    public function getVolumeHoraire(): ?int
    {
        return $this->volumeHoraire;
    }

    public function setVolumeHoraire(int $volumeHoraire): static
    {
        $this->volumeHoraire = $volumeHoraire;

        return $this;
    }

    public function getTotalCredit(): ?int
    {
        return $this->totalCredit;
    }

    public function setTotalCredit(int $totalCredit): static
    {
        $this->totalCredit = $totalCredit;

        return $this;
    }



    /**
     * @return Collection<int, MatiereUe>
     */
    public function getMatiereUes(): Collection
    {
        return $this->matiereUes;
    }

    public function addMatiereUe(MatiereUe $matiereUe): static
    {
        if (!$this->matiereUes->contains($matiereUe)) {
            $this->matiereUes->add($matiereUe);
            $matiereUe->setUniteEnseignement($this);
        }

        return $this;
    }

    public function removeMatiereUe(MatiereUe $matiereUe): static
    {
        if ($this->matiereUes->removeElement($matiereUe)) {
            // set the owning side to null (unless already changed)
            if ($matiereUe->getUniteEnseignement() === $this) {
                $matiereUe->setUniteEnseignement(null);
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
            $controle->setUe($this);
        }

        return $this;
    }

    public function removeControle(Controle $controle): static
    {
        if ($this->controles->removeElement($controle)) {
            // set the owning side to null (unless already changed)
            if ($controle->getUe() === $this) {
                $controle->setUe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MoyenneMatiere>
     */
    public function getMoyenneMatieres(): Collection
    {
        return $this->moyenneMatieres;
    }

    public function addMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if (!$this->moyenneMatieres->contains($moyenneMatiere)) {
            $this->moyenneMatieres->add($moyenneMatiere);
            $moyenneMatiere->setUe($this);
        }

        return $this;
    }

    public function removeMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if ($this->moyenneMatieres->removeElement($moyenneMatiere)) {
            // set the owning side to null (unless already changed)
            if ($moyenneMatiere->getUe() === $this) {
                $moyenneMatiere->setUe(null);
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
            $controleExamen->setUe($this);
        }

        return $this;
    }

    public function removeControleExamen(ControleExamen $controleExamen): static
    {
        if ($this->controleExamens->removeElement($controleExamen)) {
            // set the owning side to null (unless already changed)
            if ($controleExamen->getUe() === $this) {
                $controleExamen->setUe(null);
            }
        }

        return $this;
    }
}
