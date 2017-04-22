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
}

