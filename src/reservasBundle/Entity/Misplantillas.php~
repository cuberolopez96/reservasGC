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
    /**
     * @var string
     */
    private $asunto;


    /**
     * Set asunto
     *
     * @param string $asunto
     *
     * @return Misplantillas
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;

        return $this;
    }

    /**
     * Get asunto
     *
     * @return string
     */
    public function getAsunto()
    {
        return $this->asunto;
    }
    public function toArray(){
      $array = array('Asunto' => $this->getAsunto(),
    'Texto'=>$this->getTexto());
    return $array;

    }
}
