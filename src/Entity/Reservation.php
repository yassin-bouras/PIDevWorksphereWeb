<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_r = null;

    public function getId_r(): ?int
    {
        return $this->id_r;
    }

    public function setId_r(int $id_r): self
    {
        $this->id_r = $id_r;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_f = null;

    public function getId_f(): ?int
    {
        return $this->id_f;
    }

    public function setId_f(?int $id_f): self
    {
        $this->id_f = $id_f;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_user = null;

    public function getId_user(): ?int
    {
        return $this->id_user;
    }

    public function setId_user(?int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $motif_r = null;

    public function getMotif_r(): ?string
    {
        return $this->motif_r;
    }

    public function setMotif_r(string $motif_r): self
    {
        $this->motif_r = $motif_r;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $attente = null;

    public function getAttente(): ?string
    {
        return $this->attente;
    }

    public function setAttente(string $attente): self
    {
        $this->attente = $attente;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $langue = null;

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'reservation')]
    private Collection $meetings;

    public function __construct()
    {
        $this->meetings = new ArrayCollection();
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetings(): Collection
    {
        if (!$this->meetings instanceof Collection) {
            $this->meetings = new ArrayCollection();
        }
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->getMeetings()->contains($meeting)) {
            $this->getMeetings()->add($meeting);
        }
        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        $this->getMeetings()->removeElement($meeting);
        return $this;
    }

    public function getIdR(): ?int
    {
        return $this->id_r;
    }

    public function getIdF(): ?int
    {
        return $this->id_f;
    }

    public function setIdF(?int $id_f): static
    {
        $this->id_f = $id_f;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getMotifR(): ?string
    {
        return $this->motif_r;
    }

    public function setMotifR(string $motif_r): static
    {
        $this->motif_r = $motif_r;

        return $this;
    }

}
