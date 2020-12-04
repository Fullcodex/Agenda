<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Libelle;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateDebut;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateFin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Lieu;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Couleur;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $Actif;

    /**
     * @ORM\ManyToOne(targetEntity="Agenda", inversedBy="Evenement")
     * @ORM\JoinColumn(name="fk_evenement_agenda", referencedColumnName="id")
     */
    private $Agenda;
    
    /**
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumn(name="fk_evenement_categorie", referencedColumnName="id")
     */
    private $Categorie;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getLibelle(): ?string
    {
        return $this->Libelle;
    }
    
     public function getNote():?string
    {
        return $this->Note;
    }
    
    public function getDateDebut()
    {
        return $this->DateDebut;
    }
    
    public function getDateFin()
    {
        return $this->DateFin;
    }
    
    public function getLieu(): ?string
    {
        return $this->Lieu;
    }
    
    public function getCouleur(): ?string
    {
        return $this->Couleur;
    }
    
    public function getAgenda(): ?int
    {
        return $this->Agenda;
    }
    
    public function getCategorie(): ?int
    {
        return $this->Categorie;
    }
    
    
    public function setLibelle($Libelle): ?self
    {
        $this->Libelle = $Libelle;
        return $this;
    }
    
    public function setNote($Note): ?self
    {
        $this->Note = $Note;
        return $this;
    }
    
    public function setDateDebut($DateDebut): ?self
    {
        $this->DateDebut = $DateDebut;
        return $this;
    }
    
    public function setDateFin($DateFin): ?self
    {
        $this->DateFin = $DateFin;
        return $this;
    }
    
    public function setLieu($Lieu): ?self
    {
        $this->Lieu = $Lieu;
        return $this;
    }
    
    public function setCouleur($Couleur): ?self
    {
        $this->Couleur = $Couleur;
        return $this;
    }
    
    public function setAgenda($Agenda): ?self
    {
        $this->Agenda = $Agenda;
        return $this;
    }
    
    public function setCategorie($Categorie): ?self
    {
        $this->Categorie = $Categorie;
        return $this;
    }
    
}
