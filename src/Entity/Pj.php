<?php

namespace App\Entity;

use App\Repository\PjRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PjRepository::class)
 */
class Pj
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $Nom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateAjout;
    
    /**
     * @ORM\ManyToOne(targetEntity="Evenement")
     * @ORM\JoinColumn(name="fk_pj_evenement", referencedColumnName="id")
     */
    private $Evenement;

    public function getId(): ?int
    {
        return $this->id;
    }
}
