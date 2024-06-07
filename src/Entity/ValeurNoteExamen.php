<?php

namespace App\Entity;

use App\Repository\ValeurNoteExamenRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ValeurNoteExamenRepository::class)]
#[Table(name: 'evaluation_examen_valeur_note')]
class ValeurNoteExamen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'valeurNoteExamens')]
    private ?NoteExamen $noteEntity = null;

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getNoteEntity(): ?NoteExamen
    {
        return $this->noteEntity;
    }

    public function setNoteEntity(?NoteExamen $noteEntity): static
    {
        $this->noteEntity = $noteEntity;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
