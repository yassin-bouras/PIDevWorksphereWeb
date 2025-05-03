<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // Ajout de l'espace de noms pour les contraintes de validation
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

    #[ORM\Column(name: 'nomEvent', type: 'string')]
    #[Assert\NotBlank(message: "Le nom de l'événement est obligatoire.")]
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

    #[ORM\Column(name: 'descEvent', type: 'text')]
    #[Assert\NotBlank(message: "La description de l'événement est obligatoire.")]
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

    #[ORM\Column(name: 'dateEvent', type: 'datetime')]
    #[Assert\NotBlank(message: "La date de l'événement est obligatoire.")]
    #[Assert\GreaterThan("today", message: "La date de l'événement doit être postérieure à aujourd'hui.")]
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

    #[ORM\Column(name: 'lieuEvent', type: 'string')]
    #[Assert\NotBlank(message: "Le lieu de l'événement est obligatoire.")]
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

    #[ORM\Column(name: 'capaciteEvent', type: 'integer')]
    #[Assert\NotBlank(message: "La capacité de l'événement est obligatoire.")]
    #[Assert\Positive(message: "La capacité doit être un nombre positif.")]
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

    #[ORM\Column(name: 'typeEvent', type: 'string', length: 20, nullable: true)]
    private ?string $typeEvent = null; 
    public function getTypeEvent(): ?string // Getter pour typeEvent
    {
        return $this->typeEvent;
    }

    public function setTypeEvent(?string $typeEvent): self // Setter pour typeEvent
    {
        $this->typeEvent = $typeEvent;
        return $this;
    }
}