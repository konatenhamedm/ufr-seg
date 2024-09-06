<?php

namespace App\Entity;

use App\Repository\EncartBacRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EncartBacRepository::class)]
class EncartBac
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un numéro diplome')]
    private ?string $matricule = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serie = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $bac = null;

    #[ORM\ManyToOne(inversedBy: 'encartBacs')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mention = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(?string $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(?string $serie): static
    {
        $this->serie = $serie;

        return $this;
    }


    public function setBac(?Fichier $bac): static
    {
        $this->bac = $bac;
        return $this;
    }

    public function getBac(): ?Fichier
    {
        return $this->bac;
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

    public function getEtablissement(): ?string
    {
        return $this->etablissement;
    }

    public function setEtablissement(?string $etablissement): static
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(?string $mention): static
    {
        $this->mention = $mention;

        return $this;
    }
}
