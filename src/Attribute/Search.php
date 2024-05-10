<?php


namespace App\Attribute;

use App\Entity\Niveau;

class Search
{

    private ?Niveau $niveau = null;
    private ?string $filiere = null;
    private ?string $classe = null;
    private ?string $typeFrais = null;
    private ?string $mode = null;
    private ?string $dateDebut = null;
    private ?string $dateFin = null;
    private ?string $caissiere = null;









    /**
     * Get the value of filiere
     */
    public function getFiliere()
    {
        return $this->filiere;
    }

    /**
     * Set the value of filiere
     *
     * @return  self
     */
    public function setFiliere($filiere)
    {
        $this->filiere = $filiere;

        return $this;
    }


    /**
     * Get the value of classe
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set the value of classe
     *
     * @return  self
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }


    /**
     * Get the value of typeFrais
     */
    public function getTypeFrais()
    {
        return $this->typeFrais;
    }

    /**
     * Set the value of typeFrais
     *
     * @return  self
     */
    public function setTypeFrais($typeFrais)
    {
        $this->typeFrais = $typeFrais;

        return $this;
    }


    /**
     * Get the value of mode
     */
    public function getMode()
    {
        return $this->mode;
    }



    /**
     * Set the value of mode
     *
     * @return  self
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }




    /**
     * Get the value of dateDebut
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set the value of dateDebut
     *
     * @return  self
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get the value of dateFin
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set the value of dateFin
     *
     * @return  self
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get the value of caissiere
     */
    public function getCaissiere()
    {
        return $this->caissiere;
    }

    /**
     * Set the value of caissiere
     *
     * @return  self
     */
    public function setCaissiere($caissiere)
    {
        $this->caissiere = $caissiere;

        return $this;
    }

    /**
     * Get the value of niveau
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set the value of niveau
     *
     * @return  self
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }
}
