<?php

namespace App\Entity;

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
    public function getIdr(): ?int
    {
        return $this->id_r;
    }

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

    public function getIdf(): ?int
    {
        return $this->id_f;
    }

    public function setIdf(?int $id_f): self
    {
        $this->id_f = $id_f;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_user = null;

    public function getIduser(): ?int
    {
        return $this->id_user;
    }

    public function setIduser(?int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $motif_r = null;

    public function getMotifr(): ?string
    {
        return $this->motif_r;
    }

    public function setMotifr(string $motif_r): self
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

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'reservations')]
#[ORM\JoinColumn(name: 'id_f', referencedColumnName: 'id_f', nullable: false)]
private ?Formation $formation = null;

public function getFormation(): ?Formation
{
    return $this->formation;
}

public function setFormation(?Formation $formation): self
{
    $this->formation = $formation;
    return $this;
}

}