<?php

namespace App\Entity;

use App\Repository\EntretienHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntretienHistoryRepository::class)]
#[ORM\Table(name: 'entretienhistory')]
class EntretienHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $action = null;

    #[ORM\Column(length: 100)]
    private ?string $field_changed = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $old_value = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $new_value = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

     #[ORM\ManyToOne]
     #[ORM\JoinColumn(name: 'id_entretien', referencedColumnName: 'id')]
    private ?Entretien $entretien = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getFieldChanged(): ?string
    {
        return $this->field_changed;
    }

    public function setFieldChanged(string $fieldChanged): static
    {
        $this->field_changed = $fieldChanged;

        return $this;
    }

    public function getOldValue(): ?string
    {
        return $this->old_value;
    }

    public function setOldValue(string $oldValue): static
    {
        $this->old_value = $oldValue;

        return $this;
    }

    public function getNewValue(): ?string
    {
        return $this->new_value;
    }

    public function setNewValue(string $newValue): static
    {
        $this->new_value = $newValue;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getEntretien(): ?Entretien
    {
        return $this->entretien;
    }

    public function setEntretien(?Entretien $entretien): static
    {
        $this->entretien = $entretien;

        return $this;
    }
}
