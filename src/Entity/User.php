<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $UuidMincraft = null;

    #[ORM\Column(length: 255)]
    private ?string $Roles = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $PseudoMincraft = null;

    #[ORM\Column]
    private ?int $Credits = null;

    #[ORM\Column]
    private ?\DateTime $DateIncription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUuidMincraft(): ?int
    {
        return $this->UuidMincraft;
    }

    public function setUuidMincraft(int $UuidMincraft): static
    {
        $this->UuidMincraft = $UuidMincraft;

        return $this;
    }

    public function getRoles(): ?string
    {
        return $this->Roles;
    }

    public function setRoles(string $Roles): static
    {
        $this->Roles = $Roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPseudoMincraft(): ?string
    {
        return $this->PseudoMincraft;
    }

    public function setPseudoMincraft(string $PseudoMincraft): static
    {
        $this->PseudoMincraft = $PseudoMincraft;

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->Credits;
    }

    public function setCredits(int $Credits): static
    {
        $this->Credits = $Credits;

        return $this;
    }

    public function getDateIncription(): ?\DateTime
    {
        return $this->DateIncription;
    }

    public function setDateIncription(\DateTime $DateIncription): static
    {
        $this->DateIncription = $DateIncription;

        return $this;
    }
}
