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

    public function setIdservicios($id){
      $this->idservicios = $id;
      return $this;
    }

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
    public function getArrayMenu(){
      if ($this->getMenumenu()==null) {
        return null;
      }
      return $this->getMenumenu()->toArray();
    }
    public function toArray(){
      $array= array(
        'id' => $this->getIdservicios(),
        'FechaServicio'=>$this->getFechaservicio()->format('Y/m/d h:i:s'),
        'Plazas'=>$this->getPlazas(),
        'menu'=>$this->getArrayMenu()
      );
      return $array;
    }
    /**
     * @var integer
     */
    private $plazasocupadas = 0;


    /**
     * Set plazasocupadas
     *
     * @param integer $plazasocupadas
     *
     * @return Servicios
     */
    public function setPlazasocupadas($plazasocupadas)
    {
        $this->plazasocupadas = $plazasocupadas;

        return $this;
    }

    /**
     * Get plazasocupadas
     *
     * @return integer
     */
    public function getPlazasocupadas()
    {
        return $this->plazasocupadas;
    }
    /**
     * @var string
     */
    private $nombre;


    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Servicios
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
     * @var boolean
     */
    private $avisoenviado;


    /**
     * Set avisoenviado
     *
     * @param boolean $avisoenviado
     *
     * @return Servicios
     */
    public function setAvisoenviado($avisoenviado)
    {
        $this->avisoenviado = $avisoenviado;

        return $this;
    }

    /**
     * Get avisoenviado
     *
     * @return boolean
     */
    public function getAvisoenviado()
    {
        return $this->avisoenviado;
    }
}
