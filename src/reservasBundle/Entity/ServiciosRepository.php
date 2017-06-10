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

    public function findByToday(){
      $date = new \Datetime();
      $date = $date->format('Y-m-d H:i:s');
      $sql ="SELECT sum(r.npersonas) as plazasocupadas,max(s.idservicios) as idservicios,max(s.fechaservicio) as fechaservicio ,max(s.plazas) as plazas
      from reservasBundle:Servicios as s inner join reservasBundle:Reservas as r with r.serviciosservicios = s.idservicios
      where s.fechaservicio >= '$date' group by s.idservicios";
      return $this->getEntityManager()->createQuery($sql)->getResult();
    }
}
 ?>
