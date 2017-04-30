<?php

namespace reservasBundle\Entity;

/**
 * Servicios
 */
class Servicios
{
    /**
     * @var integer
     */
    private $idservicios;

    /**
     * @var \DateTime
     */
    private $fechaservicio;

    /**
     * @var integer
     */
    private $plazas;

    /**
     * @var \reservasBundle\Entity\Menu
     */
    private $menumenu;


    /**
     * Get idservicios
     *
     * @return integer
     */
    public function getIdservicios()
    {
        return $this->idservicios;
    }

    /**
     * Set fechaservicio
     *
     * @param \DateTime $fechaservicio
     *
     * @return Servicios
     */
    public function setFechaservicio($fechaservicio)
    {
        $this->fechaservicio = $fechaservicio;

        return $this;
    }

    /**
     * Get fechaservicio
     *
     * @return \DateTime
     */
    public function getFechaservicio()
    {
        return $this->fechaservicio;
    }

    /**
     * Set plazas
     *
     * @param integer $plazas
     *
     * @return Servicios
     */
    public function setPlazas($plazas)
    {
        $this->plazas = $plazas;

        return $this;
    }

    /**
     * Get plazas
     *
     * @return integer
     */
    public function getPlazas()
    {
        return $this->plazas;
    }

    /**
     * Set menumenu
     *
     * @param \reservasBundle\Entity\Menu $menumenu
     *
     * @return Servicios
     */
    public function setMenumenu(\reservasBundle\Entity\Menu $menumenu = null)
    {
        $this->menumenu = $menumenu;

        return $this;
    }

    /**
     * Get menumenu
     *
     * @return \reservasBundle\Entity\Menu
     */
    public function getMenumenu()
    {
        return $this->menumenu;
    }
    public function toArray(){
      $array= array(
        'FechaServicio'=>$this->getFechaservicio()->format('Y/m/d h:i:s'),
        'Plazas'=>$this->getPlazas(),
      );
      return $array;
    }
}
