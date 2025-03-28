<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\FavoriRepository;

#[ORM\Entity(repositoryClass: FavoriRepository::class)]
#[ORM\Table(name: 'favoris')]
class Favori
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_favori = null;

    public function getId_favori(): ?int
    {
        return $this->id_favori;
    }

    public function setId_favori(int $id_favori): self
    {
        $this->id_favori = $id_favori;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id_user = null;

    public function getId_user(): ?int
    {
        return $this->id_user;
    }

    public function setId_user(int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id_f = null;

    public function getId_f(): ?int
    {
        return $this->id_f;
    }

    public function setId_f(int $id_f): self
    {
        $this->id_f = $id_f;
        return $this;
    }

    public function getIdFavori(): ?int
    {
        return $this->id_favori;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdF(): ?int
    {
        return $this->id_f;
    }

    public function setIdF(int $id_f): static
    {
        $this->id_f = $id_f;

        return $this;
    }

}
