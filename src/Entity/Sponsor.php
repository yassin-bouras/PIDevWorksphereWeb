<?php

namespace App\Entity;

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
    #[ORM\Column(name: 'idSponsor', type: 'integer')]
    private ?int $idSponsor = null;
    public function getId(): ?int {
        return $this->idSponsor;
    }
    public function getIdSponsor(): ?int
    {
        return $this->idSponsor;
    }

    public function setIdSponsor(int $idSponsor): self
    {
        $this->idSponsor = $idSponsor;
        return $this;
    }

    #[ORM\Column(name: 'nomSponso',type: 'string', nullable: true)]
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

    #[ORM\Column(name: 'prenomSponso',type: 'string', nullable: true)]
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

    #[ORM\Column(name: 'emailSponso',type: 'string', nullable: true)]
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

    #[ORM\Column(name: 'budgetSponso', type: 'decimal', precision: 10, scale: 2, nullable: true)]
private ?string $budgetSponso = null;

public function getBudgetSponso(): ?string
{
    return $this->budgetSponso;
}

public function setBudgetSponso(?string $budgetSponso): self
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

    #[ORM\Column(name: 'BudgetApresReduction', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $BudgetApresReduction = null;

    public function getBudgetApresReduction(): ?string
{
    return $this->BudgetApresReduction;
}

public function setBudgetApresReduction(?string $BudgetApresReduction): self
{
    $this->BudgetApresReduction = $BudgetApresReduction;
    return $this;
}

#[ORM\Column(name: 'secteurSponsor', type: 'string', length: 20, nullable: true)]
    private ?string $secteurSponsor = null;
    public function getSecteurSponsor(): ?string // Getter pour secteurSponsor
    {
        return $this->secteurSponsor;
    }

    public function setSecteurSponsor(?string $secteurSponsor): self // Setter pour secteurSponsor
    {
        $this->secteurSponsor = $secteurSponsor;
        return $this;
    }

}