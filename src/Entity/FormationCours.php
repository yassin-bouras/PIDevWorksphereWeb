<?php

namespace App\Entity;

use App\Repository\FormationCoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationCoursRepository::class)]
class FormationCours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_fc', type: 'integer')]
    private ?int $id_fc = null;

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'formationCours')]
    #[ORM\JoinColumn(name: 'id_f', referencedColumnName: 'id_f', nullable: false, onDelete: 'CASCADE')]
    private ?Formation $formation = null;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: 'formationCours')]
    #[ORM\JoinColumn(name: 'id_c', referencedColumnName: 'id_c', nullable: false, onDelete: 'CASCADE')]
    private ?Cours $cours = null;

    public function getIdFc(): ?int
    {
        return $this->id_fc;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;
        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;
        return $this;
    }
}
