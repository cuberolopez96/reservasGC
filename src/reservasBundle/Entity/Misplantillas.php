<?php

namespace reservasBundle\Entity;

/**
 * Misplantillas
 */
class Misplantillas
{
    /**
     * @var integer
     */
    private $idmisplantillas;

    /**
     * @var string
     */
    private $texto;


    /**
     * Get idmisplantillas
     *
     * @return integer
     */
    public function getIdmisplantillas()
    {
        return $this->idmisplantillas;
    }

    /**
     * Set texto
     *
     * @param string $texto
     *
     * @return Misplantillas
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }
}

