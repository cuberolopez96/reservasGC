<?php

namespace reservasBundle\Entity;

/**
 * Reservas
 */
class Reservas
{
    /**
     * @var integer
     */
    private $idreservas;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $apellidos;

    /**
     * @var string
     */
    private $correo;

    /**
     * @var string
     */
    private $telefono;

    /**
     * @var string
     */
    private $observaciones;

    /**
     * @var string
     */
    private $codreserva;

    /**
     * @var \reservasBundle\Entity\Estadoreserva
     */
    private $estadoreservaestadoreserva;

    /**
     * @var \reservasBundle\Entity\Servicios
     */
    private $serviciosservicios;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $alergenosalergenos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alergenosalergenos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idreservas
     *
     * @return integer
     */
    public function getIdreservas()
    {
        return $this->idreservas;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Reservas
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
     * Set apellidos
     *
     * @param string $apellidos
     *
     * @return Reservas
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set correo
     *
     * @param string $correo
     *
     * @return Reservas
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Reservas
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Reservas
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set codreserva
     *
     * @param string $codreserva
     *
     * @return Reservas
     */
    public function setCodreserva($codreserva)
    {
        $this->codreserva = $codreserva;

        return $this;
    }

    /**
     * Get codreserva
     *
     * @return string
     */
    public function getCodreserva()
    {
        return $this->codreserva;
    }

    /**
     * Set estadoreservaestadoreserva
     *
     * @param \reservasBundle\Entity\Estadoreserva $estadoreservaestadoreserva
     *
     * @return Reservas
     */
    public function setEstadoreservaestadoreserva(\reservasBundle\Entity\Estadoreserva $estadoreservaestadoreserva = null)
    {
        $this->estadoreservaestadoreserva = $estadoreservaestadoreserva;

        return $this;
    }

    /**
     * Get estadoreservaestadoreserva
     *
     * @return \reservasBundle\Entity\Estadoreserva
     */
    public function getEstadoreservaestadoreserva()
    {
        return $this->estadoreservaestadoreserva;
    }

    /**
     * Set serviciosservicios
     *
     * @param \reservasBundle\Entity\Servicios $serviciosservicios
     *
     * @return Reservas
     */
    public function setServiciosservicios(\reservasBundle\Entity\Servicios $serviciosservicios = null)
    {
        $this->serviciosservicios = $serviciosservicios;

        return $this;
    }

    /**
     * Get serviciosservicios
     *
     * @return \reservasBundle\Entity\Servicios
     */
    public function getServiciosservicios()
    {
        return $this->serviciosservicios;
    }

    /**
     * Add alergenosalergeno
     *
     * @param \reservasBundle\Entity\Alergenos $alergenosalergeno
     *
     * @return Reservas
     */
    public function addAlergenosalergeno(\reservasBundle\Entity\Alergenos $alergenosalergeno)
    {
        $this->alergenosalergenos[] = $alergenosalergeno;

        return $this;
    }

    /**
     * Remove alergenosalergeno
     *
     * @param \reservasBundle\Entity\Alergenos $alergenosalergeno
     */
    public function removeAlergenosalergeno(\reservasBundle\Entity\Alergenos $alergenosalergeno)
    {
        $this->alergenosalergenos->removeElement($alergenosalergeno);
    }

    /**
     * Get alergenosalergenos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlergenosalergenos()
    {
        return $this->alergenosalergenos;
    }
}

