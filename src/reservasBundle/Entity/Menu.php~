<?php

namespace reservasBundle\Entity;

/**
 * Menu
 */
class Menu
{
    /**
     * @var integer
     */
    private $idmenu;

    /**
     * @var string
     */
    private $descripción;

    /**
     * @var string
     */
    private $imagen;


    /**
     * Get idmenu
     *
     * @return integer
     */
    public function getIdmenu()
    {
        return $this->idmenu;
    }

    /**
     * Set descripción
     *
     * @param string $descripción
     *
     * @return Menu
     */
    public function setDescripción($descripción)
    {
        $this->descripción = $descripción;

        return $this;
    }

    /**
     * Get descripción
     *
     * @return string
     */
    public function getDescripción()
    {
        return $this->descripción;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     *
     * @return Menu
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    public function toArray(){
      $array = array(
        'Descripcion'=>$this->getDescripción(),
        'Imagen'=>$this->getImagen()
      );
      return $array;
    }
    /**
     * @var string
     */
    private $precio;


    /**
     * Set precio
     *
     * @param string $precio
     *
     * @return Menu
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return string
     */
    public function getPrecio()
    {
        return $this->precio;
    }
}
