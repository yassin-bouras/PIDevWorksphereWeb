<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_c = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le nom du cours est obligatoire.")]
    private ?string $nom_c = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description_c = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotNull(message: "La date du cours est obligatoire.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date doit être aujourd'hui ou dans le futur.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'time', nullable: true)]
    #[Assert\NotNull(message: "L'heure de début est obligatoire.")]
    private ?\DateTimeInterface $heure_debut = null;

    #[ORM\Column(type: 'time', nullable: true)]
    #[Assert\NotNull(message: "L'heure de fin est obligatoire.")]
    #[Assert\Expression(
        "this.getHeureFin() > this.getHeureDebut()",
        message: "L'heure de fin doit être supérieure à l'heure de début."
    )]
    private ?\DateTimeInterface $heure_fin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Image(
        maxSize: "10M",
        mimeTypes: ["image/jpeg", "image/png", "image/gif", "image/jpg"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)."
    )]
    private ?string $photo = null;

    // 👇 Relation avec FormationCours
    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: FormationCours::class, cascade: ['persist', 'remove'])]
    private Collection $formationCours;

    public function __construct()
    {
        $this->formationCours = new ArrayCollection();
    }

    // ✅ GETTERS & SETTERS

    public function getIdC(): ?int
    {
        return $this->id_c;
    }

    public function getId(): ?int
    {
        return $this->id_c;
    }

    public function getNomC(): ?string
    {
        return $this->nom_c;
    }

    public function setNomC(?string $nom_c): static
    {
        $this->nom_c = $nom_c;
        return $this;
    }

    public function getDescriptionC(): ?string
    {
        return $this->description_c;
    }

    public function setDescriptionC(?string $description_c): static
    {
        $this->description_c = $description_c;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(?\DateTimeInterface $heure_debut): static
    {
        $this->heure_debut = $heure_debut;
        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heure_fin;
    }

    public function setHeureFin(?\DateTimeInterface $heure_fin): static
    {
        $this->heure_fin = $heure_fin;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    // ✅ Gestion de la relation FormationCours

    public function getFormationCours(): Collection
    {
        return $this->formationCours;
    }

    public function addFormationCour(FormationCours $formationCour): self
    {
        if (!$this->formationCours->contains($formationCour)) {
            $this->formationCours[] = $formationCour;
            $formationCour->setCours($this);
        }

        return $this;
    }

    public function removeFormationCour(FormationCours $formationCour): self
    {
        if ($this->formationCours->removeElement($formationCour)) {
            if ($formationCour->getCours() === $this) {
                $formationCour->setCours(null);
            }
        }

        return $this;
    }
}
