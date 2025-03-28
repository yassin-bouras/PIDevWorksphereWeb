<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\HistoriqueEntretienRepository;

#[ORM\Entity(repositoryClass: HistoriqueEntretienRepository::class)]
#[ORM\Table(name: 'historique_entretien')]
class HistoriqueEntretien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $titre = null;

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_entretien = null;

    public function getDate_entretien(): ?\DateTimeInterface
    {
        return $this->date_entretien;
    }

    public function setDate_entretien(?\DateTimeInterface $date_entretien): self
    {
        $this->date_entretien = $date_entretien;
        return $this;
    }

    #[ORM\Column(type: 'time', nullable: true)]
    private ?string $heure_entretien = null;

    public function getHeure_entretien(): ?string
    {
        return $this->heure_entretien;
    }

    public function setHeure_entretien(?string $heure_entretien): self
    {
        $this->heure_entretien = $heure_entretien;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type_entretien = null;

    public function getType_entretien(): ?string
    {
        return $this->type_entretien;
    }

    public function setType_entretien(?string $type_entretien): self
    {
        $this->type_entretien = $type_entretien;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $status = null;

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $action = null;

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_action = null;

    public function getDate_action(): ?\DateTimeInterface
    {
        return $this->date_action;
    }

    public function setDate_action(\DateTimeInterface $date_action): self
    {
        $this->date_action = $date_action;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $employe_id = null;

    public function getEmploye_id(): ?int
    {
        return $this->employe_id;
    }

    public function setEmploye_id(?int $employe_id): self
    {
        $this->employe_id = $employe_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $feedbackId = null;

    public function getFeedbackId(): ?int
    {
        return $this->feedbackId;
    }

    public function setFeedbackId(?int $feedbackId): self
    {
        $this->feedbackId = $feedbackId;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $candidatId = null;

    public function getCandidatId(): ?int
    {
        return $this->candidatId;
    }

    public function setCandidatId(?int $candidatId): self
    {
        $this->candidatId = $candidatId;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $idOffre = null;

    public function getIdOffre(): ?int
    {
        return $this->idOffre;
    }

    public function setIdOffre(?int $idOffre): self
    {
        $this->idOffre = $idOffre;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $idCandidature = null;

    public function getIdCandidature(): ?int
    {
        return $this->idCandidature;
    }

    public function setIdCandidature(?int $idCandidature): self
    {
        $this->idCandidature = $idCandidature;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $entretien_id = null;

    public function getEntretien_id(): ?int
    {
        return $this->entretien_id;
    }

    public function setEntretien_id(int $entretien_id): self
    {
        $this->entretien_id = $entretien_id;
        return $this;
    }

    public function getDateEntretien(): ?\DateTimeInterface
    {
        return $this->date_entretien;
    }

    public function setDateEntretien(?\DateTimeInterface $date_entretien): static
    {
        $this->date_entretien = $date_entretien;

        return $this;
    }

    public function getHeureEntretien(): ?\DateTimeInterface
    {
        return $this->heure_entretien;
    }

    public function setHeureEntretien(?\DateTimeInterface $heure_entretien): static
    {
        $this->heure_entretien = $heure_entretien;

        return $this;
    }

    public function getTypeEntretien(): ?string
    {
        return $this->type_entretien;
    }

    public function setTypeEntretien(?string $type_entretien): static
    {
        $this->type_entretien = $type_entretien;

        return $this;
    }

    public function getDateAction(): ?\DateTimeInterface
    {
        return $this->date_action;
    }

    public function setDateAction(\DateTimeInterface $date_action): static
    {
        $this->date_action = $date_action;

        return $this;
    }

    public function getEmployeId(): ?int
    {
        return $this->employe_id;
    }

    public function setEmployeId(?int $employe_id): static
    {
        $this->employe_id = $employe_id;

        return $this;
    }

    public function getEntretienId(): ?int
    {
        return $this->entretien_id;
    }

    public function setEntretienId(int $entretien_id): static
    {
        $this->entretien_id = $entretien_id;

        return $this;
    }

}
