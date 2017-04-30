<?php

namespace reservasBundle\Entity;

/**
 * Alergenos
 */
class Alergenos
{
    /**
     * @var integer
     */
    private $idalergenos;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reservasreservas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservasreservas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idalergenos
     *
     * @return integer
     */
    public function getIdalergenos()
    {
        return $this->idalergenos;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Alergenos
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

    /**
     * Add reservasreserva
     *
     * @param \reservasBundle\Entity\Reservas $reservasreserva
     *
     * @return Alergenos
     */
    public function addReservasreserva(\reservasBundle\Entity\Reservas $reservasreserva)
    {
        $this->reservasreservas[] = $reservasreserva;

        return $this;
    }

    /**
     * Remove reservasreserva
     *
     * @param \reservasBundle\Entity\Reservas $reservasreserva
     */
    public function removeReservasreserva(\reservasBundle\Entity\Reservas $reservasreserva)
    {
        $this->reservasreservas->removeElement($reservasreserva);
    }

    /**
     * Get reservasreservas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReservasreservas()
    {
        return $this->reservasreservas;
    }
    public function toArray(){
      $array=array(
        'Id'=>$this->getIdalergenos(),
        'Nombre'=>$this->getNombre()
      );
      return $array;
    }
}
