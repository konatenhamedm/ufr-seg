<?php

namespace App\Entity;

use App\Repository\TypeFraisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: TypeFraisRepository::class)]
#[Table(name: 'param_type_frais')]
#[UniqueEntity('code', message: 'Ce code est déjà utilisé')]
class TypeFrais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'typeFrais', targetEntity: InfoInscription::class)]
    private Collection $infoInscriptions;

    #[ORM\OneToMany(mappedBy: 'typeFrais', targetEntity: FraisBloc::class)]
    private Collection $fraisBlocs;

    public function __construct()
    {
        $this->infoInscriptions = new ArrayCollection();
        $this->fraisBlocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, InfoInscription>
     */
    public function getInfoInscriptions(): Collection
    {
        return $this->infoInscriptions;
    }

    public function addInfoInscription(InfoInscription $infoInscription): static
    {
        if (!$this->infoInscriptions->contains($infoInscription)) {
            $this->infoInscriptions->add($infoInscription);
            $infoInscription->setTypeFrais($this);
        }

        return $this;
    }

    public function removeInfoInscription(InfoInscription $infoInscription): static
    {
        if ($this->infoInscriptions->removeElement($infoInscription)) {
            // set the owning side to null (unless already changed)
            if ($infoInscription->getTypeFrais() === $this) {
                $infoInscription->setTypeFrais(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FraisBloc>
     */
    public function getFraisBlocs(): Collection
    {
        return $this->fraisBlocs;
    }

    public function addFraisBloc(FraisBloc $fraisBloc): static
    {
        if (!$this->fraisBlocs->contains($fraisBloc)) {
            $this->fraisBlocs->add($fraisBloc);
            $fraisBloc->setTypeFrais($this);
        }

        return $this;
    }

    public function removeFraisBloc(FraisBloc $fraisBloc): static
    {
        if ($this->fraisBlocs->removeElement($fraisBloc)) {
            // set the owning side to null (unless already changed)
            if ($fraisBloc->getTypeFrais() === $this) {
                $fraisBloc->setTypeFrais(null);
            }
        }

        return $this;
    }
}
