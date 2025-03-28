<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\MeetingRepository;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
#[ORM\Table(name: 'meetings')]
class Meeting
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
    private ?string $room_name = null;

    public function getRoom_name(): ?string
    {
        return $this->room_name;
    }

    public function setRoom_name(?string $room_name): self
    {
        $this->room_name = $room_name;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $meeting_url = null;

    public function getMeeting_url(): ?string
    {
        return $this->meeting_url;
    }

    public function setMeeting_url(?string $meeting_url): self
    {
        $this->meeting_url = $meeting_url;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: 'meetings')]
    #[ORM\JoinColumn(name: 'reservation_id', referencedColumnName: 'id_r')]
    private ?Reservation $reservation = null;

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;
        return $this;
    }

    public function getRoomName(): ?string
    {
        return $this->room_name;
    }

    public function setRoomName(?string $room_name): static
    {
        $this->room_name = $room_name;

        return $this;
    }

    public function getMeetingUrl(): ?string
    {
        return $this->meeting_url;
    }

    public function setMeetingUrl(?string $meeting_url): static
    {
        $this->meeting_url = $meeting_url;

        return $this;
    }

}
