<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
#[ORM\Table(name: 'projet')]
class Projet
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
    #[Assert\NotBlank(message: "Le nom du projet est obligatoire.")]
    #[Assert\Length(
        min: 3,
        minMessage: "Le nom doit contenir au moins 3 caractères.",
        max: 30,
        maxMessage: "Le nom ne peut pas dépasser 30 caractères."
    )]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "La description du projet est obligatoire.")]
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

    #[ORM\Column(name: 'datecréation',type: 'date', nullable: true)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    private ?\DateTimeInterface $dateCreation = null;

    public function getDateCreation(): ?\DateTimeInterface
    {
    return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
    $this->dateCreation = $dateCreation;
    return $this;
    }


    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotBlank(message: "Le deadline est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "dateCreation", message: "Le deadline doit être postérieur à la date de début .")]
    private ?\DateTimeInterface $deadline = null;

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: 'projets')]
    #[ORM\JoinColumn(name: 'equipe_id', referencedColumnName: 'id')]
    private ?Equipe $equipe = null;

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): self
    {
        $this->equipe = $equipe;
        return $this;
    }


    
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $etat = null;

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

   

   
    #[ORM\Column(name: 'imageProjet', type: 'string', nullable: false)]
    #[Assert\Image(
        maxSize: "10M",
        mimeTypes: ["image/jpeg", "image/png", "image/gif", "image/jpg"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)."
    )]
    private ?string $imageProjet = null;

    public function getImageProjet(): ?string
    {
        return $this->imageProjet;
    }

    public function setImageProjet(string $imageProjet): self
    {
        $this->imageProjet = $imageProjet;
        return $this;
    }

    /*#[ORM\ManyToMany(targetEntity: Equipe::class, inversedBy: 'projets')]
    #[ORM\JoinTable(name: 'equipe_projet')]
    private Collection $equipe;

    public function __construct()
    {
        $this->equipe = new ArrayCollection();
    }

    /**
     * @return Collection<int, Equipe>
     */
    
    /*public function getEquipes(): Collection
    {
        return $this->equipe;
    }

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->equipe->contains($equipe)) {
            $this->equipe->add($equipe);
            $equipe->addProjet($this);
        }
        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        if ($this->equipe->removeElement($equipe)) {
            $equipe->removeProjet($this);
        }
        return $this;
    }
    */
}