<?php

namespace reservasBundle\Entity;

/**
 * ReservasHasAlergenos
 */
class ReservasHasAlergenos
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \reservasBundle\Entity\Alergenos
     */
    private $alergenosalergenos;

    /**
     * @var \reservasBundle\Entity\Reservas
     */
    private $reservasreservas;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set alergenosalergenos
     *
     * @param \reservasBundle\Entity\Alergenos $alergenosalergenos
     *
     * @return ReservasHasAlergenos
     */
    public function setAlergenosalergenos(\reservasBundle\Entity\Alergenos $alergenosalergenos = null)
    {
        $this->alergenosalergenos = $alergenosalergenos;

        return $this;
    }

    /**
     * Get alergenosalergenos
     *
     * @return \reservasBundle\Entity\Alergenos
     */
    public function getAlergenosalergenos()
    {
        return $this->alergenosalergenos;
    }

    /**
     * Set reservasreservas
     *
     * @param \reservasBundle\Entity\Reservas $reservasreservas
     *
     * @return ReservasHasAlergenos
     */
    public function setReservasreservas(\reservasBundle\Entity\Reservas $reservasreservas = null)
    {
        $this->reservasreservas = $reservasreservas;

        return $this;
    }
    public function toArray(){
      return array(
        'alergeno' => $this->getAlergenosalergenos()->toArray(),
        'reserva' => $this->getReservasreservas()->toArray()

      );
    }
    /**
     * Get reservasreservas
     *
     * @return \reservasBundle\Entity\Reservas
     */
    public function getReservasreservas()
    {
        return $this->reservasreservas;
    }
}
