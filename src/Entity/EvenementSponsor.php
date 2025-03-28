<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\EvenementSponsorRepository;

#[ORM\Entity(repositoryClass: EvenementSponsorRepository::class)]
#[ORM\Table(name: 'evenement_sponsor')]
class EvenementSponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $evenement_id = null;

    public function getEvenement_id(): ?int
    {
        return $this->evenement_id;
    }

    public function setEvenement_id(int $evenement_id): self
    {
        $this->evenement_id = $evenement_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $sponsor_id = null;

    public function getSponsor_id(): ?int
    {
        return $this->sponsor_id;
    }

    public function setSponsor_id(int $sponsor_id): self
    {
        $this->sponsor_id = $sponsor_id;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $datedebutContrat = null;

    public function getDatedebutContrat(): ?\DateTimeInterface
    {
        return $this->datedebutContrat;
    }

    public function setDatedebutContrat(?\DateTimeInterface $datedebutContrat): self
    {
        $this->datedebutContrat = $datedebutContrat;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $duree = null;

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getEvenementId(): ?int
    {
        return $this->evenement_id;
    }

    public function getSponsorId(): ?int
    {
        return $this->sponsor_id;
    }

    public function setSponsorId(int $sponsor_id): static
    {
        $this->sponsor_id = $sponsor_id;

        return $this;
    }

}
