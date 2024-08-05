<?php

namespace App\Entity;

use App\Repository\MatiereUeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MatiereUeRepository::class)]
#[UniqueEntity(fields: ['matiere', 'uniteEnseignement'], message: "Il s'embelle que cette matiere n'existe pas dans cette UE")]
class MatiereUe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matiereUes')]
    private ?UniteEnseignement $uniteEnseignement = null;

    #[ORM\ManyToOne(inversedBy: 'matiereUes')]
    private ?Matiere $matiere = null;

    #[ORM\Column(length: 255)]
    private ?string $noteEliminatoire = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenneValidation = null;

    #[ORM\Column(options: ["default" => "true"])]
    private ?bool $visible = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;


    public function __construct()
    {
        $this->visible = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniteEnseignement(): ?UniteEnseignement
    {
        return $this->uniteEnseignement;
    }

    public function setUniteEnseignement(?UniteEnseignement $uniteEnseignement): static
    {
        $this->uniteEnseignement = $uniteEnseignement;

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


    public function getNoteEliminatoire(): ?string
    {
        return $this->noteEliminatoire;
    }

    public function setNoteEliminatoire(string $noteEliminatoire): static
    {
        $this->noteEliminatoire = $noteEliminatoire;

        return $this;
    }

    public function getMoyenneValidation(): ?string
    {
        return $this->moyenneValidation;
    }

    public function setMoyenneValidation(string $moyenneValidation): static
    {
        $this->moyenneValidation = $moyenneValidation;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

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
}
