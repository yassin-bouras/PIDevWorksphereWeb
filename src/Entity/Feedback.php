<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\FeedbackRepository;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table(name: 'feedback')]
class Feedback
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

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank(message: 'Le message ne doit pas être vide.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'Le message doit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $message = null;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $rate = null;

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_feedback = null;

    public function getDate_feedback(): ?\DateTimeInterface
    {
        return $this->date_feedback;
    }

    public function setDate_feedback(\DateTimeInterface $date_feedback): self
    {
        $this->date_feedback = $date_feedback;
        return $this;
    }

    #[ORM\OneToOne(targetEntity: Entretien::class, mappedBy: 'feedback')]
    private ?Entretien $entretien = null;

    public function getEntretien(): ?Entretien
    {
        return $this->entretien;
    }

    public function setEntretien(?Entretien $entretien): self
    {
        $this->entretien = $entretien;
        return $this;
    }

    public function getDateFeedback(): ?\DateTimeInterface
    {
        return $this->date_feedback;
    }

    public function setDateFeedback(\DateTimeInterface $date_feedback): static
    {
        $this->date_feedback = $date_feedback;

        return $this;
    }

}
