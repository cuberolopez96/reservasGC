<?php

namespace reservasBundle\Entity;

/**
 * Estadoreserva
 */
class Estadoreserva
{
    /**
     * @var integer
     */
    private $idestadoreserva;

    /**
     * @var string
     */
    private $nombre;


    /**
     * Get idestadoreserva
     *
     * @return integer
     */
    public function getIdestadoreserva()
    {
        return $this->idestadoreserva;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Estadoreserva
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    public function toArray(){
        $array=array(
          'Id'=>$this->getIdestadoreserva(),
          'Nombre'=>$this->getNombre()
        );
        return $array;
    }
}
