<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\EquipeRepository;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
#[ORM\Table(name: 'equipe')]
class Equipe
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
    private ?string $nom_equipe = null;

    public function getNom_equipe(): ?string
    {
        return $this->nom_equipe;
    }

    public function setNom_equipe(string $nom_equipe): self
    {
        $this->nom_equipe = $nom_equipe;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $imageEquipe = null;

    public function getImageEquipe(): ?string
    {
        return $this->imageEquipe;
    }

    public function setImageEquipe(?string $imageEquipe): self
    {
        $this->imageEquipe = $imageEquipe;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nbrProjet = null;

    public function getNbrProjet(): ?int
    {
        return $this->nbrProjet;
    }

    public function setNbrProjet(?int $nbrProjet): self
    {
        $this->nbrProjet = $nbrProjet;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Projet::class, mappedBy: 'equipe')]
    private Collection $projets;

    /**
     * @return Collection<int, Projet>
     */
    public function getProjets(): Collection
    {
        if (!$this->projets instanceof Collection) {
            $this->projets = new ArrayCollection();
        }
        return $this->projets;
    }

    public function addProjet(Projet $projet): self
    {
        if (!$this->getProjets()->contains($projet)) {
            $this->getProjets()->add($projet);
        }
        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        $this->getProjets()->removeElement($projet);
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'equipes')]
    #[ORM\JoinTable(
        name: 'equipe_employee',
        joinColumns: [
            new ORM\JoinColumn(name: 'equipe_id', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')
        ]
    )]
    private Collection $users;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

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

    public function getNomEquipe(): ?string
    {
        return $this->nom_equipe;
    }

    public function setNomEquipe(string $nom_equipe): static
    {
        $this->nom_equipe = $nom_equipe;

        return $this;
    }

}
