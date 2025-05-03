<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\EvenementSponsorRepository;

#[ORM\Entity(repositoryClass: EvenementSponsorRepository::class)]
#[ORM\Table(name: 'evenement_sponsor')]
class EvenementSponsor
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Evennement::class)]
    #[ORM\JoinColumn(name: 'evenement_id', referencedColumnName: 'idEvent')]
    private ?Evennement $evenement = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Sponsor::class)]
    #[ORM\JoinColumn(name: 'sponsor_id', referencedColumnName: 'idSponsor')]
    private ?Sponsor $sponsor = null;

    #[ORM\Column(name: 'datedebutContrat', type: 'date', nullable: true)]
    private ?\DateTimeInterface $datedebutContrat = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $duree = null;

    public function getEvenement(): ?Evennement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evennement $evenement): self
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    public function setSponsor(?Sponsor $sponsor): self
    {
        $this->sponsor = $sponsor;
        return $this;
    }

    public function getDatedebutContrat(): ?\DateTimeInterface
    {
        return $this->datedebutContrat;
    }

    public function setDatedebutContrat(?\DateTimeInterface $datedebutContrat): self
    {
        $this->datedebutContrat = $datedebutContrat;
        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self
    {
        $this->duree = $duree;
        return $this;
    }
}