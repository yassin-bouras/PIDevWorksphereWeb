<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
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
   
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(max: 500, maxMessage: "La description ne doit pas dépasser 500 caractères.")]
    private ?string $description = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: "La date ne peut pas être vide.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date doit être aujourd'hui ou dans le futur.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de places doit être supérieur à zéro.")]
    private ?int $nbPlace = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(choices: ['présentiel', 'distanciel'], message: "Le type doit être 'présentiel' ou 'distanciel'.")]
    private ?string $type = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $idUser = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Image(
        maxSize: "10M",
        mimeTypes: ["image/jpeg", "image/png", "image/gif", "image/jpg"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)."
    )]
    private ?string $photo = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(message: "La langue est obligatoire.")]
    private ?string $langue = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Choice(choices: ['oui', 'non'], message: "Le champ de certification doit être 'oui' ou 'non'.")]
    private ?string $certifie = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'formation')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->cours = new ArrayCollection();
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

public function setDate(?\DateTimeInterface $date): self
{
    $this->date = $date;
    return $this;
}

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;
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

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(?int $idUser): self
    {
        $this->idUser = $idUser;
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

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(?string $langue): self
    {
        $this->langue = $langue;
        return $this;
    }

    public function getCertifie(): ?string
    {
        return $this->certifie;
    }

    public function setCertifie(?string $certifie): self
    {
        $this->certifie = $certifie;
        return $this;
    }

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
            if ($reservation->getFormation() === $this) {
                $reservation->setFormation(null);
            }
        }
        return $this;
    }


    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: Cours::class, orphanRemoval: true)]
    private Collection $cours;

    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCours(Cours $cours): static
    {
        if (!$this->cours->contains($cours)) {
            $this->cours[] = $cours;
            $cours->setFormation($this);
        }

        return $this;
    }

    public function removeCours(Cours $cours): static
    {
        if ($this->cours->removeElement($cours)) {
            if ($cours->getFormation() === $this) {
                $cours->setFormation(null);
            }
        }

        return $this;

    }

    public function addCour(Cours $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setFormation($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getFormation() === $this) {
                $cour->setFormation(null);
            }
        }

        return $this;
    }
}