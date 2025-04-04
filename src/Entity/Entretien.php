<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\EntretienRepository;

#[ORM\Entity(repositoryClass: EntretienRepository::class)]
#[ORM\Table(name: 'entretiens')]
class Entretien
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $titre = null;

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_entretien = null;

    public function getDate_entretien(): ?\DateTimeInterface
    {
        return $this->date_entretien;
    }

    public function setDate_entretien(\DateTimeInterface $date_entretien): self
    {
        $this->date_entretien = $date_entretien;
        return $this;
    }

    #[ORM\Column(name:"heure_entretien",type: 'time', nullable: false)]
        private ?\DateTimeInterface $heureentretien = null;

        public function getHeureentretien(): ?\DateTimeInterface
        {
            return $this->heureentretien;
        }

        public function setHeureentretien(\DateTimeInterface $heureentretien): self
        {
            $this->heureentretien = $heureentretien;
            return $this;
        }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $type_entretien = null;

    public function getType_entretien(): ?string
    {
        return $this->type_entretien;
    }

    public function setType_entretien(string $type_entretien): self
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'entretiens')]
    #[ORM\JoinColumn(name: 'employe_id', referencedColumnName: 'id_user')]
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

    #[ORM\OneToOne(targetEntity: Feedback::class, inversedBy: 'entretien')]
    #[ORM\JoinColumn(name: 'feedbackId', referencedColumnName: 'id', unique: true)]
    private ?Feedback $feedback = null;

    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback): self
    {
        $this->feedback = $feedback;
        return $this;
    }

    #[ORM\Column(name: 'candidatId', type: 'integer', nullable: true)]
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

    #[ORM\ManyToOne(targetEntity: Offre::class, inversedBy: 'entretiens')]
    #[ORM\JoinColumn(name: 'idOffre', referencedColumnName: 'id_offre')]
    private ?Offre $offre = null;

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Candidature::class, inversedBy: 'entretiens')]
    #[ORM\JoinColumn(name: 'idCandidature', referencedColumnName: 'id_candidature')]
    private ?Candidature $candidature = null;

    public function getCandidature(): ?Candidature
    {
        return $this->candidature;
    }

    public function setCandidature(?Candidature $candidature): self
    {
        $this->candidature = $candidature;
        return $this;
    }

    public function getDateEntretien(): ?\DateTimeInterface
    {
        return $this->date_entretien;
    }

    public function setDateEntretien(\DateTimeInterface $date_entretien): static
    {
        $this->date_entretien = $date_entretien;

        return $this;
    }

    public function getTypeEntretien(): ?string
    {
        return $this->type_entretien;
    }

    public function setTypeEntretien(string $type_entretien): static
    {
        $this->type_entretien = $type_entretien;

        return $this;
    }

}
