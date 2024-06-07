<?php

namespace App\Entity;

use App\Repository\DecisionExamenRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: DecisionExamenRepository::class)]
#[Table(name: 'evaluation_examen_decision')]
class DecisionExamen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'decisionExamens')]
    private ?Etudiant $etudiant = null;



    #[ORM\Column(length: 255)]
    private ?string $noteExamen = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenneControle = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreCredit = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenneAnnuelle = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNoteExamen(): ?string
    {
        return $this->noteExamen;
    }

    public function setNoteExamen(string $noteExamen): static
    {
        $this->noteExamen = $noteExamen;

        return $this;
    }

    public function getMoyenneControle(): ?string
    {
        return $this->moyenneControle;
    }

    public function setMoyenneControle(string $moyenneControle): static
    {
        $this->moyenneControle = $moyenneControle;

        return $this;
    }

    public function getNombreCredit(): ?string
    {
        return $this->nombreCredit;
    }

    public function setNombreCredit(string $nombreCredit): static
    {
        $this->nombreCredit = $nombreCredit;

        return $this;
    }

    public function getMoyenneAnnuelle(): ?string
    {
        return $this->moyenneAnnuelle;
    }

    public function setMoyenneAnnuelle(string $moyenneAnnuelle): static
    {
        $this->moyenneAnnuelle = $moyenneAnnuelle;

        return $this;
    }
}
