<?php

namespace App\Entity;

use App\Repository\ValeurNoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ValeurNoteRepository::class)]
#[Table(name: 'evaluation_valeur_notes')]
class ValeurNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'valeurNotes', cascade:['persist'])]
    private ?Note $noteEntity = null;

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;


    #[ORM\OneToOne(mappedBy: 'valeurNote')]
    private ?CoefValeurNote $coefValeurNote = null;

    public function __construct()
    {
       
    }

    public function getCoefValeurNote(): ?CoefValeurNote
    {
        return $this->coefValeurNote;
    }

    public function setCoefValeurNote(CoefValeurNote $coefValeurNote): static
    {
        // set the owning side of the relation if necessary
        if ($coefValeurNote->getValeurNote() !== $this) {
            $coefValeurNote->setValeurNote($this);
        }

        $this->coefValeurNote = $coefValeurNote;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoteEntity(): ?Note
    {
        return $this->noteEntity;
    }

    public function setNoteEntity(?Note $noteEntity): static
    {
        $this->noteEntity = $noteEntity;

        return $this;
    }

    /**
     * Get the value of note
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the value of note
     *
     * @return  self
     */
    public function setNote($note)
    {
        $this->note = $note;

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
