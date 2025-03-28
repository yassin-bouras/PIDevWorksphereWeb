<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private ?int $iduser = null;

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(int $iduser): self
    {
        $this->iduser = $iduser;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->iduser;
    }
    public function setId(int $id): self
    {
        $this->iduser = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $mdp = null;

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $role = null;

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $adresse = null;

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $sexe = null;

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;
        return $this;
    }

    #[ORM\Column(name: 'image_profil', type: 'string', nullable: true)]
    private ?string $imageprofil = null;

    public function getImageprofil(): ?string
    {
        return $this->imageprofil;
    }

    public function setImageprofil(?string $imageprofil): self
    {
        $this->imageprofil = $imageprofil;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(name: 'salaire_attendu', type: 'decimal', nullable: true)]
    private ?float $salaireattendu = null;

    public function getSalaireattendu(): ?float
    {
        return $this->salaireattendu;
    }

    public function setSalaireattendu(?float $salaireattendu): self
    {
        $this->salaireattendu = $salaireattendu;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $poste = null;

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(?string $poste): self
    {
        $this->poste = $poste;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $salaire = null;

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(?float $salaire): self
    {
        $this->salaire = $salaire;
        return $this;
    }

    #[ORM\Column(name: 'experience_travail', type: 'integer', nullable: true)]
    private ?int $experiencetravail = null;

    public function getExperiencetravail(): ?int
    {
        return $this->experiencetravail;
    }

    public function setExperiencetravail(?int $experiencetravail): self
    {
        $this->experiencetravail = $experiencetravail;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $departement = null;

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $competence = null;

    public function getCompetence(): ?string
    {
        return $this->competence;
    }

    public function setCompetence(?string $competence): self
    {
        $this->competence = $competence;
        return $this;
    }

    #[ORM\Column(name: 'nombreProjet', type: 'integer', nullable: true)]
    private ?int $nombreprojet = null;

    public function getNombreprojet(): ?int
    {
        return $this->nombreprojet;
    }

    public function setNombreprojet(?int $nombreprojet): self
    {
        $this->nombreprojet = $nombreprojet;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $budget = null;

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    #[ORM\Column(name: 'departement_géré', type: 'string', nullable: true)]
    private ?string $departementgere = null;

    public function getDepartementgere(): ?string
    {
        return $this->departementgere;
    }

    public function setDepartementgere(?string $departementgere): self
    {
        $this->departementgere = $departementgere;
        return $this;
    }

    #[ORM\Column(name: 'ans_experience', type: 'integer', nullable: true)]
    private ?int $ansexperience = null;

    public function getAnsexperience(): ?int
    {
        return $this->ansexperience;
    }

    public function setAnsexperience(?int $ansexperience): self
    {
        $this->ansexperience = $ansexperience;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $specialisation = null;

    public function getSpecialisation(): ?string
    {
        return $this->specialisation;
    }

    public function setSpecialisation(?string $specialisation): self
    {
        $this->specialisation = $specialisation;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $banned = null;

    public function isBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(?bool $banned): self
    {
        $this->banned = $banned;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $messagereclamation = null;

    public function getMessagereclamation(): ?string
    {
        return $this->messagereclamation;
    }

    public function setMessagereclamation(?string $messagereclamation): self
    {
        $this->messagereclamation = $messagereclamation;
        return $this;
    }

    #[ORM\Column(name: 'numt_tel', type: 'integer', nullable: true)]
    private ?int $numtel = null;

    public function getNumtel(): ?int
    {
        return $this->numtel;
    }

    public function setNumtel(?int $numtel): self
    {
        $this->numtel = $numtel;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Candidature::class, mappedBy: 'user')]
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

    #[ORM\OneToMany(targetEntity: Entretien::class, mappedBy: 'user')]
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

    #[ORM\OneToMany(targetEntity: Evennement::class, mappedBy: 'user')]
    private Collection $evennements;

    /**
     * @return Collection<int, Evennement>
     */
    public function getEvennements(): Collection
    {
        if (!$this->evennements instanceof Collection) {
            $this->evennements = new ArrayCollection();
        }
        return $this->evennements;
    }

    public function addEvennement(Evennement $evennement): self
    {
        if (!$this->getEvennements()->contains($evennement)) {
            $this->getEvennements()->add($evennement);
        }
        return $this;
    }

    public function removeEvennement(Evennement $evennement): self
    {
        $this->getEvennements()->removeElement($evennement);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        if (!$this->notifications instanceof Collection) {
            $this->notifications = new ArrayCollection();
        }
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->getNotifications()->contains($notification)) {
            $this->getNotifications()->add($notification);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        $this->getNotifications()->removeElement($notification);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'user')]
    private Collection $reclamations;

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        if (!$this->reclamations instanceof Collection) {
            $this->reclamations = new ArrayCollection();
        }
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->getReclamations()->contains($reclamation)) {
            $this->getReclamations()->add($reclamation);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        $this->getReclamations()->removeElement($reclamation);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'user')]
    private Collection $reponses;

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        if (!$this->reponses instanceof Collection) {
            $this->reponses = new ArrayCollection();
        }
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->getReponses()->contains($reponse)) {
            $this->getReponses()->add($reponse);
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        $this->getReponses()->removeElement($reponse);
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Offre::class, inversedBy: 'users')]
    #[ORM\JoinTable(
        name: 'bookmarks',
        joinColumns: [
            new ORM\JoinColumn(name: 'id_candidat', referencedColumnName: 'id_user')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre')
        ]
    )]
    private Collection $offres;

    /**
     * @return Collection<int, Offre>
     */
    public function getOffres(): Collection
    {
        if (!$this->offres instanceof Collection) {
            $this->offres = new ArrayCollection();
        }
        return $this->offres;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->getOffres()->contains($offre)) {
            $this->getOffres()->add($offre);
        }
        return $this;
    }

    public function removeOffre(Offre $offre): self
    {
        $this->getOffres()->removeElement($offre);
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Equipe::class, inversedBy: 'users')]
    #[ORM\JoinTable(
        name: 'equipe_employee',
        joinColumns: [
            new ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'equipe_id', referencedColumnName: 'id')
        ]
    )]
    private Collection $equipes;

    /**
     * @return Collection<int, Equipe>
     */
    public function getEquipes(): Collection
    {
        if (!$this->equipes instanceof Collection) {
            $this->equipes = new ArrayCollection();
        }
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->getEquipes()->contains($equipe)) {
            $this->getEquipes()->add($equipe);
        }
        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        $this->getEquipes()->removeElement($equipe);
        return $this;
    }
}
