<?php

namespace App\Entity;

use App\Repository\EquipeProjetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeProjetRepository::class)]
class EquipeProjet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'equipe_id', type: 'integer')]
    private ?int $equipe_id  = null;

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: 'equipeProjets')]
    #[ORM\JoinColumn(name: 'equipe_id', referencedColumnName: 'equipe_id', nullable: false, onDelete: 'CASCADE')]
    private ?Equipe $equipe = null;

    #[ORM\ManyToOne(targetEntity: Projet::class, inversedBy: 'equipeProjets')]
    #[ORM\JoinColumn(name: 'projet_id', referencedColumnName: 'projet_id', nullable: false, onDelete: 'CASCADE')]
    private ?Projet $projet = null;

    public function getIdFc(): ?int
    {
        return $this->equipe_id ;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): self
    {
        $this->equipe = $equipe;
        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;
        return $this;
    }
}