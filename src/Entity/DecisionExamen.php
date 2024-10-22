<?php

namespace App\Entity;

use App\Repository\DecisionExamenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: DecisionExamenRepository::class)]
#[Table(name: 'evaluation_examen_decision')]
class DecisionExamen
{

    const DECISION = [
        'Valide' => 'Validé',
        'Invalide' => 'Invalidé',
    ];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'decisionExamens')]
    private ?Etudiant $etudiant = null;



    #[ORM\Column(length: 255)]
    private ?string $noteExamen = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenneAnnuelle = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $decision = null;


    #[ORM\ManyToOne(inversedBy: 'decisionExamens')]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'decisionExamens')]
    private ?Niveau $niveau = null;

    #[ORM\ManyToOne(inversedBy: 'decisionExamens')]
    private ?UniteEnseignement $ue = null;

    #[ORM\ManyToOne(inversedBy: 'decisionExamens')]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'decisionExamens')]
    private ?Classe $classe = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $codeEcue = null;


    public function __construct()
    {
    }

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

    public function getMoyenneAnnuelle(): ?string
    {
        return $this->moyenneAnnuelle;
    }

    public function setMoyenneAnnuelle(string $moyenneAnnuelle): static
    {
        $this->moyenneAnnuelle = $moyenneAnnuelle;

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



    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
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

    public function getUe(): ?UniteEnseignement
    {
        return $this->ue;
    }

    public function setUe(?UniteEnseignement $ue): static
    {
        $this->ue = $ue;

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

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

    public function getCodeEcue(): ?string
    {
        return $this->codeEcue;
    }

    public function setCodeEcue(string $codeEcue): static
    {
        $this->codeEcue = $codeEcue;

        return $this;
    }
}
