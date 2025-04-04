<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\EvennementRepository;

#[ORM\Entity(repositoryClass: EvennementRepository::class)]
#[ORM\Table(name: 'evennement')]
class Evennement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idEvent', type: 'integer')]
    private ?int $idEvent = null;

    public function getIdEvent(): ?int
    {
        return $this->idEvent;
    }

    public function setIdEvent(int $idEvent): self
    {
        $this->idEvent = $idEvent;
        return $this;
    }

    #[ORM\Column(name: 'nomEvent',type: 'string', nullable: true)]
    private ?string $nomEvent = null;

    public function getNomEvent(): ?string
    {
        return $this->nomEvent;
    }

    public function setNomEvent(?string $nomEvent): self
    {
        $this->nomEvent = $nomEvent;
        return $this;
    }

    #[ORM\Column(name: 'descEvent',type: 'text', nullable: true)]
    private ?string $descEvent = null;

    public function getDescEvent(): ?string
    {
        return $this->descEvent;
    }

    public function setDescEvent(?string $descEvent): self
    {
        $this->descEvent = $descEvent;
        return $this;
    }

    #[ORM\Column(name: 'dateEvent',type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateEvent = null;

    public function getDateEvent(): ?\DateTimeInterface
    {
        return $this->dateEvent;
    }

    public function setDateEvent(?\DateTimeInterface $dateEvent): self
    {
        $this->dateEvent = $dateEvent;
        return $this;
    }

    #[ORM\Column(name: 'lieuEvent',type: 'string', nullable: true)]
    private ?string $lieuEvent = null;

    public function getLieuEvent(): ?string
    {
        return $this->lieuEvent;
    }

    public function setLieuEvent(?string $lieuEvent): self
    {
        $this->lieuEvent = $lieuEvent;
        return $this;
    }

    #[ORM\Column(name: 'capaciteEvent',type: 'integer', nullable: true)]
    private ?int $capaciteEvent = null;

    public function getCapaciteEvent(): ?int
    {
        return $this->capaciteEvent;
    }

    public function setCapaciteEvent(?int $capaciteEvent): self
    {
        $this->capaciteEvent = $capaciteEvent;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'evennements')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function __toString(): string
    {
        return $this->nomEvent; 
    }
}
