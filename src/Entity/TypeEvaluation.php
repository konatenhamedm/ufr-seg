<?php

namespace App\Entity;

use App\Repository\TypeEvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: TypeEvaluationRepository::class)]
#[Table(name: 'evaluation_type_evaluation')]
class TypeEvaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'typeEvaluation', targetEntity: GroupeType::class)]
    private Collection $groupeTypes;

    public function __construct()
    {
        $this->groupeTypes = new ArrayCollection();
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
     * @return Collection<int, GroupeType>
     */
    public function getGroupeTypes(): Collection
    {
        return $this->groupeTypes;
    }

    public function addGroupeType(GroupeType $groupeType): static
    {
        if (!$this->groupeTypes->contains($groupeType)) {
            $this->groupeTypes->add($groupeType);
            $groupeType->setTypeEvaluation($this);
        }

        return $this;
    }

    public function removeGroupeType(GroupeType $groupeType): static
    {
        if ($this->groupeTypes->removeElement($groupeType)) {
            // set the owning side to null (unless already changed)
            if ($groupeType->getTypeEvaluation() === $this) {
                $groupeType->setTypeEvaluation(null);
            }
        }

        return $this;
    }
}
