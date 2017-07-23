<?php

namespace reservasBundle\Entity;

/**
 * Config
 */
class Config
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $confirmacion;

    /**
     * @var string
     */
    private $recordatorio;

    /**
     * @var string
     */
    private $listanegra;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set confirmacion
     *
     * @param string $confirmacion
     *
     * @return Config
     */
    public function setConfirmacion($confirmacion)
    {
        $this->confirmacion = $confirmacion;

        return $this;
    }

    /**
     * Get confirmacion
     *
     * @return string
     */
    public function getConfirmacion()
    {
        return $this->confirmacion;
    }

    /**
     * Set recordatorio
     *
     * @param string $recordatorio
     *
     * @return Config
     */
    public function setRecordatorio($recordatorio)
    {
        $this->recordatorio = $recordatorio;

        return $this;
    }

    /**
     * Get recordatorio
     *
     * @return string
     */
    public function getRecordatorio()
    {
        return $this->recordatorio;
    }

    /**
     * Set listanegra
     *
     * @param string $listanegra
     *
     * @return Config
     */
    public function setListanegra($listanegra)
    {
        $this->listanegra = $listanegra;

        return $this;
    }

    /**
     * Get listanegra
     *
     * @return string
     */
    public function getListanegra()
    {
        return $this->listanegra;
    }
    /**
     * @var string
     */
    private $cancelacion;

    /**
     * @var string
     */
    private $edicionservicio;

    /**
     * @var string
     */
    private $edicionreserva;


    /**
     * Set cancelacion
     *
     * @param string $cancelacion
     *
     * @return Config
     */
    public function setCancelacion($cancelacion)
    {
        $this->cancelacion = $cancelacion;

        return $this;
    }

    /**
     * Get cancelacion
     *
     * @return string
     */
    public function getCancelacion()
    {
        return $this->cancelacion;
    }

    /**
     * Set edicionservicio
     *
     * @param string $edicionservicio
     *
     * @return Config
     */
    public function setEdicionservicio($edicionservicio)
    {
        $this->edicionservicio = $edicionservicio;

        return $this;
    }

    /**
     * Get edicionservicio
     *
     * @return string
     */
    public function getEdicionservicio()
    {
        return $this->edicionservicio;
    }

    /**
     * Set edicionreserva
     *
     * @param string $edicionreserva
     *
     * @return Config
     */
    public function setEdicionreserva($edicionreserva)
    {
        $this->edicionreserva = $edicionreserva;

        return $this;
    }

    /**
     * Get edicionreserva
     *
     * @return string
     */
    public function getEdicionreserva()
    {
        return $this->edicionreserva;
    }
    /**
     * @var string
     */
    private $clistaespera;


    /**
     * Set clistaespera
     *
     * @param string $clistaespera
     *
     * @return Config
     */
    public function setClistaespera($clistaespera)
    {
        $this->clistaespera = $clistaespera;

        return $this;
    }

    /**
     * Get clistaespera
     *
     * @return string
     */
    public function getClistaespera()
    {
        return $this->clistaespera;
    }
    /**
     * @var string
     */
    private $Email_Administrador;

    /**
     * @var string
     */
    private $Firma_Administrador;


    /**
     * Set emailAdministrador
     *
     * @param string $emailAdministrador
     *
     * @return Config
     */
    public function setEmailAdministrador($emailAdministrador)
    {
        $this->Email_Administrador = $emailAdministrador;

        return $this;
    }

    /**
     * Get emailAdministrador
     *
     * @return string
     */
    public function getEmailAdministrador()
    {
        return $this->Email_Administrador;
    }

    /**
     * Set firmaAdministrador
     *
     * @param string $firmaAdministrador
     *
     * @return Config
     */
    public function setFirmaAdministrador($firmaAdministrador)
    {
        $this->Firma_Administrador = $firmaAdministrador;

        return $this;
    }

    /**
     * Get firmaAdministrador
     *
     * @return string
     */
    public function getFirmaAdministrador()
    {
        return $this->Firma_Administrador;
    }
}
