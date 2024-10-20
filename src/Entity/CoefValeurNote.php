<?php

namespace App\Entity;

use App\Repository\CoefValeurNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoefValeurNoteRepository::class)]
class CoefValeurNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $coef = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ValeurNote $valeurNote = null;

  

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoef(): ?int
    {
        return $this->coef;
    }

    public function setCoef(int $coef): static
    {
        $this->coef = $coef;

        return $this;
    }

    public function getValeurNote(): ?ValeurNote
    {
        return $this->valeurNote;
    }

    public function setValeurNote(?ValeurNote $valeurNote): static
    {
        $this->valeurNote = $valeurNote;

        return $this;
    }

    
}
