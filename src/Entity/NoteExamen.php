<?php

namespace App\Entity;

use App\Repository\NoteExamenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: NoteExamenRepository::class)]
#[Table(name: 'evaluation_examen_note')]
class NoteExamen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'noteExamens', cascade:['persist'])]
    private ?ControleExamen $controleExamen = null;

    #[ORM\ManyToOne(inversedBy: 'noteExamens')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rang = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $exposant = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenneUe = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenneConrole = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $decision = null;

    #[ORM\OneToMany(mappedBy: 'noteEntity', targetEntity: ValeurNoteExamen::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $valeurNoteExamens;

    public function __construct()
    {
        $this->valeurNoteExamens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getRang(): ?string
    {
        return $this->rang;
    }

    public function setRang(string $rang): static
    {
        $this->rang = $rang;

        return $this;
    }

    public function getExposant(): ?string
    {
        return $this->exposant;
    }

    public function setExposant(?string $exposant): static
    {
        $this->exposant = $exposant;

        return $this;
    }

    public function getMoyenneUe(): ?string
    {
        return $this->moyenneUe;
    }

    public function setMoyenneUe(string $moyenneUe): static
    {
        $this->moyenneUe = $moyenneUe;

        return $this;
    }

    public function getMoyenneConrole(): ?string
    {
        return $this->moyenneConrole;
    }

    public function setMoyenneConrole(string $moyenneConrole): static
    {
        $this->moyenneConrole = $moyenneConrole;

        return $this;
    }

    public function getDecision(): ?string
    {
        return $this->decision;
    }

    public function setDecision(string $decision): static
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * @return Collection<int, ValeurNoteExamen>
     */
    public function getValeurNoteExamens(): Collection
    {
        return $this->valeurNoteExamens;
    }

    public function addValeurNoteExamen(ValeurNoteExamen $valeurNoteExamen): static
    {
        if (!$this->valeurNoteExamens->contains($valeurNoteExamen)) {
            $this->valeurNoteExamens->add($valeurNoteExamen);
            $valeurNoteExamen->setNoteEntity($this);
        }

        return $this;
    }

    public function removeValeurNoteExamen(ValeurNoteExamen $valeurNoteExamen): static
    {
        if ($this->valeurNoteExamens->removeElement($valeurNoteExamen)) {
            // set the owning side to null (unless already changed)
            if ($valeurNoteExamen->getNoteEntity() === $this) {
                $valeurNoteExamen->setNoteEntity(null);
            }
        }

        return $this;
    }
}
