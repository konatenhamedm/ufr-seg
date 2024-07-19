<?php

namespace App\Entity;

use App\Repository\TypeControleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: TypeControleRepository::class)]
#[Table(name: 'evaluation_type_controle')]
class TypeControle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $coef = null;



    #[ORM\OneToMany(mappedBy: 'typeControle', targetEntity: ControleExamen::class)]
    private Collection $controleExamens;

    #[ORM\OneToMany(mappedBy: 'typeControle', targetEntity: Controle::class)]
    private Collection $controles;



    public function __construct()
    {
        $this->controles = new ArrayCollection();
        $this->controleExamens = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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
            $controleExamen->setTypeControle($this);
        }

        return $this;
    }

    public function removeControleExamen(ControleExamen $controleExamen): static
    {
        if ($this->controleExamens->removeElement($controleExamen)) {
            // set the owning side to null (unless already changed)
            if ($controleExamen->getTypeControle() === $this) {
                $controleExamen->setTypeControle(null);
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
            $controle->setTypeControle($this);
        }

        return $this;
    }

    public function removeControle(Controle $controle): static
    {
        if ($this->controles->removeElement($controle)) {
            // set the owning side to null (unless already changed)
            if ($controle->getTypeControle() === $this) {
                $controle->setTypeControle(null);
            }
        }

        return $this;
    }
}
