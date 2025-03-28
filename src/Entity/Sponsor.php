<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\SponsorRepository;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
#[ORM\Table(name: 'sponsor')]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idSponsor = null;

    public function getIdSponsor(): ?int
    {
        return $this->idSponsor;
    }

    public function setIdSponsor(int $idSponsor): self
    {
        $this->idSponsor = $idSponsor;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nomSponso = null;

    public function getNomSponso(): ?string
    {
        return $this->nomSponso;
    }

    public function setNomSponso(?string $nomSponso): self
    {
        $this->nomSponso = $nomSponso;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $prenomSponso = null;

    public function getPrenomSponso(): ?string
    {
        return $this->prenomSponso;
    }

    public function setPrenomSponso(?string $prenomSponso): self
    {
        $this->prenomSponso = $prenomSponso;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $emailSponso = null;

    public function getEmailSponso(): ?string
    {
        return $this->emailSponso;
    }

    public function setEmailSponso(?string $emailSponso): self
    {
        $this->emailSponso = $emailSponso;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $budgetSponso = null;

    public function getBudgetSponso(): ?float
    {
        return $this->budgetSponso;
    }

    public function setBudgetSponso(?float $budgetSponso): self
    {
        $this->budgetSponso = $budgetSponso;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $classement = null;

    public function getClassement(): ?string
    {
        return $this->classement;
    }

    public function setClassement(?string $classement): self
    {
        $this->classement = $classement;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $BudgetApresReduction = null;

    public function getBudgetApresReduction(): ?float
    {
        return $this->BudgetApresReduction;
    }

    public function setBudgetApresReduction(?float $BudgetApresReduction): self
    {
        $this->BudgetApresReduction = $BudgetApresReduction;
        return $this;
    }

}
