<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "la description est obligatoire.")]
    #[Assert\Length(max: 500, maxMessage: "La description ne doit pas dépasser 200 caractères.")]
    private ?string $description = null;

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotNull(message: "La date ne peut pas être vide.")]
    #[Assert\Type(type: "\DateTimeInterface", message: "Le format de la date est invalide.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date doit être aujourd'hui ou dans le futur.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'time', nullable: false)]
    #[Assert\NotNull(message: "L'heure de début est obligatoire.")]
    #[Assert\Type(type: "\DateTimeInterface", message: "Le format de l'heure est invalide.")]
    private ?\DateTimeInterface $heure_debut = null;

    #[ORM\Column(type: 'time', nullable: false)]
    #[Assert\NotNull(message: "L'heure de fin est obligatoire.")]
    #[Assert\Type(type: "\DateTimeInterface", message: "Le format de l'heure est invalide.")]
    private ?\DateTimeInterface $heure_fin = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de places doit être supérieur à zéro.")]
    private ?int $nb_place = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Choice(choices: ['présentiel', 'distanciel'], message: "Le type doit être 'présentiel' ou 'en ligne'.")]
    private ?string $type = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_user = null;

    #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "Vous devez insérer une image.")]
    #[Assert\Image(
        maxSize: "10M",
        mimeTypes: ["image/jpeg", "image/png", "image/gif", "image/jpg"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)."
    )]
    private ?string $photo = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'formation')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id_f;
    }

    public function getIdf(): ?int
    {
        return $this->id_f;
    }

    public function setIdf(int $id_f): self
    {
        $this->id_f = $id_f;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getHeuredebut(): ?\DateTimeInterface
    {
        return $this->heure_debut;
    }

    public function setHeuredebut(\DateTimeInterface $heure_debut): self
    {
        $this->heure_debut = $heure_debut;
        return $this;
    }

    public function getHeurefin(): ?\DateTimeInterface
    {
        return $this->heure_fin;
    }

    public function setHeurefin(\DateTimeInterface $heure_fin): self
    {
        $this->heure_fin = $heure_fin;
        return $this;
    }

    public function getNbplace(): ?int
    {
        return $this->nb_place;
    }

    public function setNbplace(int $nb_place): self
    {
        $this->nb_place = $nb_place;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setFormation($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // Set the owning side to null (unless already changed)
            if ($reservation->getFormation() === $this) {
                $reservation->setFormation(null);
            }
        }
        return $this;
    }
}
