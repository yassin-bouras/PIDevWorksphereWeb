<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_r = null;

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotBlank(message: 'La date ne peut pas être vide.')]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_f = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_user = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le motif ne peut pas être vide.')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Le motif doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le motif ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $motif_r = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le champ attente est requis.')]
    private ?string $attente = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'La langue est requise.')]
    private ?string $langue = null;

    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'reservation')]
    private Collection $meetings;

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id_f', referencedColumnName: 'id_f', nullable: false)]
    private ?Formation $formation = null;

    public function __construct()
    {
        $this->date = new \DateTime(); // Date par défaut : aujourd'hui
        $this->meetings = new ArrayCollection();
    }

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getIdf(): ?int
    {
        return $this->id_f;
    }

    public function setIdf(?int $id_f): self
    {
        $this->id_f = $id_f;
        return $this;
    }

    public function getIduser(): ?int
    {
        return $this->id_user;
    }

    public function setIduser(?int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    public function getMotifr(): ?string
    {
        return $this->motif_r;
    }

    public function setMotifr(string $motif_r): self
    {
        $this->motif_r = $motif_r;
        return $this;
    }

    public function getAttente(): ?string
    {
        return $this->attente;
    }

    public function setAttente(string $attente): self
    {
        $this->attente = $attente;
        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;
        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings->add($meeting);
        }
        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        $this->meetings->removeElement($meeting);
        return $this;
    }

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
