<?php

namespace App\Entity;

use App\Repository\DroitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DroitRepository::class)
 */
class Droit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $Libelle;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getLibelle(): ?string
    {
        return $this->Libelle;
    }
}
