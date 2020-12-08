<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PersonneRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Personne implements UserInterface {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $Nom_Personnes;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $Date_Naissance;

    /**
     * @ORM\Column(type="boolean",nullable=true, options={"default": 0})
     */
    private $Actif;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function getId(): ?int {
        return $this->id;
    }
    
    public function setId(int $id): ?self {
         $this->id = $id;
         
         return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * Get Nom_Personnes
     * @return string|null
     */
    public function getNompersonnes(): ?string {
        return $this->Nom_Personnes;
    }

    public function getDatenaissance(): ?object {
        if (!empty($this->Date_Naissance)) {
            return $this->Date_Naissance;
        } else {
            return null;
        }
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    public function setNompersonnes(string $NomPrenom): self {
        $this->Nom_Personnes = $NomPrenom;
        return $this;
    }

    public function setDatenaissance(object $Date_Naissance): self {
        $this->Date_Naissance = $Date_Naissance;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if ($this->id == 4) {
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string) $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt() {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
