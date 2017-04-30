<?php

namespace reservasBundle\Entity;

/**
 * Listanegra
 */
class Listanegra
{
    /**
     * @var integer
     */
    private $idlistanegra;

    /**
     * @var string
     */
    private $correo;


    /**
     * Get idlistanegra
     *
     * @return integer
     */
    public function getIdlistanegra()
    {
        return $this->idlistanegra;
    }

    /**
     * Set correo
     *
     * @param string $correo
     *
     * @return Listanegra
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
    public function toArray(){
      $array=  array(
        'Id'=>$this->getIdlistanegra(),
        'Correo'=>$this->getCorreo()
      );
      return $array;
    }
}
