<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\OffreRepository;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
#[ORM\Table(name: 'offre')]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_offre = null;

    public function getIdoffre(): ?int
    {
        return $this->id_offre;
    }
    public function getId_offre(): ?int
    {
        return $this->id_offre;
    }
    public function getId(): ?int
    {
        return $this->id_offre;
    }

    public function setIdoffre(int $id_offre): self
    {
        $this->id_offre = $id_offre;
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

    #[ORM\Column(type: 'string', nullable: false)]
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $type_contrat = null;

    public function getTypecontrat(): ?string
    {
        return $this->type_contrat;
    }

    public function setTypecontrat(string $type_contrat): self
    {
        $this->type_contrat = $type_contrat;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $salaire = null;

    public function getSalaire(): ?int
    {
        return $this->salaire;
    }

    public function setSalaire(int $salaire): self
    {
        $this->salaire = $salaire;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $lieu_travail = null;

    public function getLieutravail(): ?string
    {
        return $this->lieu_travail;
    }

    public function setLieutravail(string $lieu_travail): self
    {
        $this->lieu_travail = $lieu_travail;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_publication = null;

    public function getDatepublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatepublication(\DateTimeInterface $date_publication): self
    {
        $this->date_publication = $date_publication;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_limite = null;

    public function getDatelimite(): ?\DateTimeInterface
    {
        return $this->date_limite;
    }

    public function setDatelimite(\DateTimeInterface $date_limite): self
    {
        $this->date_limite = $date_limite;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $statut_offre = null;

    public function getStatutoffre(): ?string
    {
        return $this->statut_offre;
    }

    public function setStatutoffre(string $statut_offre): self
    {
        $this->statut_offre = $statut_offre;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $experience = null;

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): self
    {
        $this->experience = $experience;
        return $this;
    }
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->candidatures = new ArrayCollection();
        $this->entretiens = new ArrayCollection();
    }

    #[ORM\OneToMany(targetEntity: Candidature::class, mappedBy: 'offre')]
    private Collection $candidatures;

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        if (!$this->candidatures instanceof Collection) {
            $this->candidatures = new ArrayCollection();
        }
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->getCandidatures()->contains($candidature)) {
            $this->getCandidatures()->add($candidature);
        }
        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        $this->getCandidatures()->removeElement($candidature);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Entretien::class, mappedBy: 'offre')]
    private Collection $entretiens;

    /**
     * @return Collection<int, Entretien>
     */
    public function getEntretiens(): Collection
    {
        if (!$this->entretiens instanceof Collection) {
            $this->entretiens = new ArrayCollection();
        }
        return $this->entretiens;
    }

    public function addEntretien(Entretien $entretien): self
    {
        if (!$this->getEntretiens()->contains($entretien)) {
            $this->getEntretiens()->add($entretien);
        }
        return $this;
    }

    public function removeEntretien(Entretien $entretien): self
    {
        $this->getEntretiens()->removeElement($entretien);
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'offres')]
    #[ORM\JoinTable(
        name: 'bookmarks',
        joinColumns: [
            new ORM\JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'id_candidat', referencedColumnName: 'id_user')
        ]
    )]
    private Collection $users;

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        if (!$this->users instanceof Collection) {
            $this->users = new ArrayCollection();
        }
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->getUsers()->contains($user)) {
            $this->getUsers()->add($user);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->getUsers()->removeElement($user);
        return $this;
    }
}
