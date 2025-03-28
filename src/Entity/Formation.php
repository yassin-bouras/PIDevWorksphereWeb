<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\FormationRepository;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
#[ORM\Table(name: 'formation')]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_f = null;

    public function getIdf(): ?int
    {
        return $this->id_f;
    }


    public function getId_f(): ?int
    {
        return $this->id_f;
    }

    public function setId_f(int $id_f): self
    {
        $this->id_f = $id_f;
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

    #[ORM\Column(type: 'time', nullable: false)]
    private ?\DateTimeInterface $heure_debut = null;

    public function getHeuredebut(): ?\DateTimeInterface
    {
        return $this->heure_debut;
    }

    public function setHeuredebut(\DateTimeInterface $heure_debut): self
    {
        $this->heure_debut = $heure_debut;
        return $this;
    }

    #[ORM\Column(type: 'time', nullable: false)]
    private ?\DateTimeInterface $heure_fin = null;

    public function getHeurefin(): ?\DateTimeInterface
    {
        return $this->heure_fin;
    }

    public function setHeurefin(\DateTimeInterface $heure_fin): self
    {
        $this->heure_fin = $heure_fin;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $nb_place = null;

    public function getNbplace(): ?int
    {
        return $this->nb_place;
    }

    public function setNbplace(int $nb_place): self
    {
        $this->nb_place = $nb_place;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $photo = null;

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'formation')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }
 /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setFormation($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self {
        if ($this->reservations->removeElement($reservation)) {
            // Set the owning side to null (unless already changed)
            if ($reservation->getFormation() === $this) {
                $reservation->setFormation(null);
            }
        }
        return $this;
    }

}