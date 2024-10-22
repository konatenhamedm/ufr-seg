<?php

namespace App\Entity;

use App\Repository\ControleExamenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ControleExamenRepository::class)]
#[Table(name: 'evaluation_examen_controle')]
class ControleExamen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'controleExamens')]
    private ?UniteEnseignement $ue = null;

    #[ORM\ManyToOne(inversedBy: 'controleExamens')]
    private ?Session $session = null;

    #[ORM\OneToMany(mappedBy: 'controleExamen', targetEntity: GroupeTypeExamen::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $groupeTypeExamens;

    #[ORM\OneToMany(mappedBy: 'controleExamen', targetEntity: NoteExamen::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $noteExamens;

    #[ORM\ManyToOne(inversedBy: 'controleExamens')]
    private ?TypeControle $typeControle = null;

    #[ORM\ManyToOne(inversedBy: 'controleExamens')]
    private ?Classe $classe = null;

    #[ORM\ManyToOne(inversedBy: 'ecue')]
    private ?Matiere $matiere = null;

   

    public function __construct()
    {
        $this->groupeTypeExamens = new ArrayCollection();
        $this->noteExamens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getUe(): ?UniteEnseignement
    {
        return $this->ue;
    }

    public function setUe(?UniteEnseignement $ue): static
    {
        $this->ue = $ue;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collection<int, GroupeTypeExamen>
     */
    public function getGroupeTypeExamens(): Collection
    {
        return $this->groupeTypeExamens;
    }

    public function addGroupeTypeExamen(GroupeTypeExamen $groupeTypeExamen): static
    {
        if (!$this->groupeTypeExamens->contains($groupeTypeExamen)) {
            $this->groupeTypeExamens->add($groupeTypeExamen);
            $groupeTypeExamen->setControleExamen($this);
        }

        return $this;
    }

    public function removeGroupeTypeExamen(GroupeTypeExamen $groupeTypeExamen): static
    {
        if ($this->groupeTypeExamens->removeElement($groupeTypeExamen)) {
            // set the owning side to null (unless already changed)
            if ($groupeTypeExamen->getControleExamen() === $this) {
                $groupeTypeExamen->setControleExamen(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, NoteExamen>
     */
    public function getNoteExamens(): Collection
    {
        return $this->noteExamens;
    }

    public function addNoteExamen(NoteExamen $noteExamen): static
    {
        if (!$this->noteExamens->contains($noteExamen)) {
            $this->noteExamens->add($noteExamen);
            $noteExamen->setControleExamen($this);
        }

        return $this;
    }

    public function removeNoteExamen(NoteExamen $noteExamen): static
    {
        if ($this->noteExamens->removeElement($noteExamen)) {
            // set the owning side to null (unless already changed)
            if ($noteExamen->getControleExamen() === $this) {
                $noteExamen->setControleExamen(null);
            }
        }

        return $this;
    }

    public function getTypeControle(): ?TypeControle
    {
        return $this->typeControle;
    }

    public function setTypeControle(?TypeControle $typeControle): static
    {
        $this->typeControle = $typeControle;

        return $this;
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

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

  
}
