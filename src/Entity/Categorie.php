<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
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
    private $Libelle;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getLibelle(): ?string
    {
        return $this->Libelle;
    }
    
    public function setId($id): ?self
    {
        $this->id = $id;
        return $this;
    }
    
    public function setLibelle($libelle): ?self
    {
        $this->Libelle = $libelle;
        return $this;
    }
}
