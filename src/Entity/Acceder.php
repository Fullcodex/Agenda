<?php

namespace App\Entity;

use App\Repository\AccederRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccederRepository::class)
 */
class Acceder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Personne")
     * @ORM\JoinColumn(name="fk_acceder_personne", referencedColumnName="id")
     */
    private $Ref_Personne;
    
    /**
     * @ORM\ManyToOne(targetEntity="Agenda")
     * @ORM\JoinColumn(name="fk_acceder_agenda", referencedColumnName="id")
     */
    private $Ref_Agendas;
    
    /**
     * @ORM\ManyToOne(targetEntity="Droit")
     * @ORM\JoinColumn(name="fk_acceder_droit", referencedColumnName="id")
     */
    private $Id_Droit;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getRefPersonne(): ?object
    {
        return $this->Ref_Personne;
    }
    
    public function getAgendas(): ?object
    {
        return $this->Ref_Agendas;
    }
    
    public function getIdDroit(): ?object
    {
        return $this->Id_Droit;
    }
    
     public function setRefPersonne($Ref_Personne): ?self
    {
         $this->Ref_Personne = $Ref_Personne;
         return $this;
    }
    
    public function setAgendas($Ref_Agendas): ?self
    {
        $this->Ref_Agendas = $Ref_Agendas;
        return $this;
    }
    
    public function setIdDroit($Id_Droit): ?self
    {
        $this->Id_Droit = $Id_Droit;
        return $this;
    }
}
