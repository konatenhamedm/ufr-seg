<?php

namespace App\Entity;

use App\Repository\MentionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MentionRepository::class)]
#[Table(name: 'param_mention')]
class Mention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    #[Assert\PositiveOrZero(message: 'La moyenne minimum doit être >= 0')]
    #[Assert\LessThan(propertyPath: 'moyenneMax', message: 'La moyenne min doit être < à la moyenne max')]
    private ?string $moyenneMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $moyenneMax = null;

    #[ORM\OneToMany(mappedBy: 'mention', targetEntity: EncartBac::class)]
    private Collection $encartBacs;

    public function __construct()
    {
        $this->encartBacs = new ArrayCollection();
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

    public function getMoyenneMin(): ?string
    {
        return $this->moyenneMin;
    }

    public function setMoyenneMin(string $moyenneMin): static
    {
        $this->moyenneMin = $moyenneMin;

        return $this;
    }

    public function getMoyenneMax(): ?string
    {
        return $this->moyenneMax;
    }

    public function setMoyenneMax(string $moyenneMax): static
    {
        $this->moyenneMax = $moyenneMax;

        return $this;
    }

    /**
     * @return Collection<int, EncartBac>
     */
    public function getEncartBacs(): Collection
    {
        return $this->encartBacs;
    }

    public function addEncartBac(EncartBac $encartBac): static
    {
        if (!$this->encartBacs->contains($encartBac)) {
            $this->encartBacs->add($encartBac);
            $encartBac->setMention($this);
        }

        return $this;
    }

    public function removeEncartBac(EncartBac $encartBac): static
    {
        if ($this->encartBacs->removeElement($encartBac)) {
            // set the owning side to null (unless already changed)
            if ($encartBac->getMention() === $this) {
                $encartBac->setMention(null);
            }
        }

        return $this;
    }
}
