<?php

namespace reservasBundle\Entity;

/**
 * Correos
 */
class Correos
{
    /**
     * @var integer
     */
    private $idcorreos;

    /**
     * @var string
     */
    private $email;


    /**
     * Get idcorreos
     *
     * @return integer
     */
    public function getIdcorreos()
    {
        return $this->idcorreos;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Correos
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $apellidos;


    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Correos
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
     * @return Correos
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

    public function toArray(){
      $array = array(
        'Nombre'=> $this->getNombre(),
        'Apellidos'=>$this->getApellidos(),
        'Correo'=> $this->getEmail()
      );
      return $array;
    }
}
