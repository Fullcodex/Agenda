<?php

namespace App\Entity;

use App\Repository\AgendaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgendaRepository::class)
 */
class Agenda {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Nom_Agendas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Cle_Partage;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $Dt_Cle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Img;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $Actif;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evenement", mappedBy="Agenda")
     */
    private $Evenement;

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->Nom_Agendas;
    }

    public function getCle(): ?string {
        return $this->Cle_Partage;
    }

    public function getDtCle(): ?object {
        return $this->Dt_Cle;
    }

    public function getImg(): ?string {
        return $this->Img;
    }
    
     public function getEvenement(): ?object {
        return $this->Evenement;
    }
    
    public function setId($id): ?self {
        $this->id = $id;

        return $this;
    }

    public function setNom($Nom_Agendas): ?self {
        $this->Nom_Agendas = $Nom_Agendas;

        return $this;
    }

    public function setCle($Cle_Partage): ?self {
        $this->Cle_Partage = $Cle_Partage;

        return $this;
    }

    public function setDtCle($Dt_Cle): ?self {
        $this->Dt_Cle = $Dt_Cle;
        return $this;
    }

    public function setImg($Img): ?self {
        $this->Img = $Img;
        return $this;
    }

}
