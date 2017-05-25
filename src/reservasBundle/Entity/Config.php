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
}
