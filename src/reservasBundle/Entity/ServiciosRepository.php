<?php
// src/AppBundle/Repository/ProductRepository.php
namespace reservasBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ServiciosRepository extends EntityRepository
{
    public function findLikeFechaservicio($date)
    {
        $dia = $date->format('Y-m-d');
        $sql = "SELECT s.idservicios as Id, s.fechaservicio as Fecha, s.plazas as Plazas from reservasBundle:Servicios as s
where CAST(s.fechaservicio as date) like  '$dia'";
//print_r($sql);
        return $this->getEntityManager()
            ->createQuery(
                $sql
            )
            ->getResult();
    }
}
 ?>
