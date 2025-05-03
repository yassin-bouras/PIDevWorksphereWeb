<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private ?int $iduser = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    #[ORM\Column(type: 'string', nullable: false, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $mdp = null;
    public function getPassword(): string
    {
        return $this->mdp;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $role = null;

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $sexe = null;

    #[ORM\Column(name: 'image_profil', type: 'string', nullable: true)]
    private ?string $imageprofil = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $status = null;

    #[ORM\Column(name: 'salaire_attendu', type: 'decimal', nullable: true)]
    private ?float $salaireattendu = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $poste = null;

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $salaire = null;

    #[ORM\Column(name: 'experience_travail', type: 'integer', nullable: true)]
    private ?int $experiencetravail = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $departement = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $competence = null;

    #[ORM\Column(name: 'nombreProjet', type: 'integer', nullable: true)]
    private ?int $nombreprojet = null;

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $budget = null;

    #[ORM\Column(name: 'departement_géré', type: 'string', nullable: true)]
    private ?string $departementgere = null;

    #[ORM\Column(name: 'ans_experience', type: 'integer', nullable: true)]
    private ?int $ansexperience = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $specialisation = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $banned = null;

    #[ORM\Column(name: 'messagereclamation', type: 'text', nullable: true)]
    private ?string $messagereclamation = null;

    #[ORM\Column(name: 'numt_tel', type: 'integer', nullable: true)]
    private ?int $numtel = null;

    #[ORM\OneToMany(targetEntity: Candidature::class, mappedBy: 'user')]
    private Collection $candidatures;

    #[ORM\OneToMany(targetEntity: Entretien::class, mappedBy: 'user')]
    private Collection $entretiens;

    #[ORM\OneToMany(targetEntity: Evennement::class, mappedBy: 'user')]
    private Collection $evennements;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'user')]
    private Collection $reclamations;

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'user')]
    private Collection $reponses;

    #[ORM\ManyToMany(targetEntity: Offre::class, inversedBy: 'users')]
    #[ORM\JoinTable(
        name: 'bookmarks',
        joinColumns: [new ORM\JoinColumn(name: 'id_candidat', referencedColumnName: 'id_user')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre')]
    )]
    private Collection $offres;

    #[ORM\ManyToMany(targetEntity: Equipe::class, inversedBy: 'users')]
    #[ORM\JoinTable(
        name: 'equipe_employee',
        joinColumns: [new ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'equipe_id', referencedColumnName: 'id')]
    )]
    private Collection $equipes;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
        $this->entretiens = new ArrayCollection();
        $this->evennements = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->offres = new ArrayCollection();
        $this->equipes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->iduser;
    }
    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function getFullName(): ?string
    {
        return $this->nom . ' ' . $this->prenom;
    }
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMdp(): string
    {
        return $this->mdp;
    }
    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;
        return $this;
    }



    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role ?: 'ROLE_USER'];
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;
        return $this;
    }

    public function getImageprofil(): ?string
    {
        return $this->imageprofil;
    }

    public function setImageprofil(?string $imageprofil): self
    {
        $this->imageprofil = $imageprofil;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getSalaireattendu(): ?float
    {
        return $this->salaireattendu;
    }

    public function setSalaireattendu(?float $salaireattendu): self
    {
        $this->salaireattendu = $salaireattendu;
        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(?string $poste): self
    {
        $this->poste = $poste;
        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(?float $salaire): self
    {
        $this->salaire = $salaire;
        return $this;
    }

    public function getExperiencetravail(): ?int
    {
        return $this->experiencetravail;
    }

    public function setExperiencetravail(?int $experiencetravail): self
    {
        $this->experiencetravail = $experiencetravail;
        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;
        return $this;
    }

    public function getCompetence(): ?string
    {
        return $this->competence;
    }

    public function setCompetence(?string $competence): self
    {
        $this->competence = $competence;
        return $this;
    }

    public function getNombreprojet(): ?int
    {
        return $this->nombreprojet;
    }

    public function setNombreprojet(?int $nombreprojet): self
    {
        $this->nombreprojet = $nombreprojet;
        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    public function getDepartementgere(): ?string
    {
        return $this->departementgere;
    }

    public function setDepartementgere(?string $departementgere): self
    {
        $this->departementgere = $departementgere;
        return $this;
    }

    public function getAnsexperience(): ?int
    {
        return $this->ansexperience;
    }

    public function setAnsexperience(?int $ansexperience): self
    {
        $this->ansexperience = $ansexperience;
        return $this;
    }

    public function getSpecialisation(): ?string
    {
        return $this->specialisation;
    }

    public function setSpecialisation(?string $specialisation): self
    {
        $this->specialisation = $specialisation;
        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(?bool $banned): self
    {
        $this->banned = $banned;
        return $this;
    }

    public function getMessagereclamation(): ?string
    {
        return $this->messagereclamation;
    }

    public function setMessagereclamation(?string $messagereclamation): self
    {
        $this->messagereclamation = $messagereclamation;
        return $this;
    }

    public function getNumtel(): ?int
    {
        return $this->numtel;
    }

    public function setNumtel(?int $numtel): self
    {
        $this->numtel = $numtel;
        return $this;
    }

    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setUser($this);
        }
        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->removeElement($candidature)) {
            if ($candidature->getUser() === $this) {
                $candidature->setUser(null);
            }
        }
        return $this;
    }

    public function getEntretiens(): Collection
    {
        return $this->entretiens;
    }

    public function addEntretien(Entretien $entretien): self
    {
        if (!$this->entretiens->contains($entretien)) {
            $this->entretiens->add($entretien);
            $entretien->setUser($this);
        }
        return $this;
    }

    public function removeEntretien(Entretien $entretien): self
    {
        if ($this->entretiens->removeElement($entretien)) {
            if ($entretien->getUser() === $this) {
                $entretien->setUser(null);
            }
        }
        return $this;
    }

    public function getEvennements(): Collection
    {
        return $this->evennements;
    }

    public function addEvennement(Evennement $evennement): self
    {
        if (!$this->evennements->contains($evennement)) {
            $this->evennements->add($evennement);
            $evennement->setUser($this);
        }
        return $this;
    }

    public function removeEvennement(Evennement $evennement): self
    {
        if ($this->evennements->removeElement($evennement)) {
            if ($evennement->getUser() === $this) {
                $evennement->setUser(null);
            }
        }
        return $this;
    }

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }
        return $this;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUser($this);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }
        return $this;
    }

    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setUser($this);
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            if ($reponse->getUser() === $this) {
                $reponse->setUser(null);
            }
        }
        return $this;
    }

    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres->add($offre);
        }
        return $this;
    }

    public function removeOffre(Offre $offre): self
    {
        $this->offres->removeElement($offre);
        return $this;
    }

    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes->add($equipe);
        }
        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        $this->equipes->removeElement($equipe);
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void {}
    public function __serialize(): array
    {
        return [
            'id' => $this->iduser,
            'email' => $this->email,
            'roles' => $this->getRoles(),
            'password' => $this->mdp,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->iduser = $data['id'];
        $this->email = $data['email'];
        $this->role = $data['roles'][0] ?? 'ROLE_USER';
        $this->mdp = $data['password'];
    }
}
