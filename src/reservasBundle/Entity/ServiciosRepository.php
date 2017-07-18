<?php
// src/AppBundle/Repository/ProductRepository.php
namespace reservasBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ServiciosRepository extends EntityRepository
{
    public function findLikeFechaservicio($date)
    {
        $dia = $date->format('Y-m-d');
        $sql = "SELECT s.idservicios as Id, s.fechaservicio as Fecha, s.plazas as Plazas, s.plazasocupadas, (s.plazas - s.plazasocupadas) as plazasdisponibles from reservasBundle:Servicios as s
where CAST(s.fechaservicio as date) like  '$dia'";
//print_r($sql);
        return $this->getEntityManager()
            ->createQuery(
                $sql
            )
            ->getResult();
    }
    public function findById($id){
      $sql = "SELECT s.idservicios, s.fechaservicio, s.plazas, s.plazasocupadas, (s.plazas - s.plazasdisponibles) as plazasdisponibles
      from reservasBundle:Servicios where s.idservicios = $id";
    }
    public function findByToday(){
      $date = new \Datetime();
      $date = $date->format('Y-m-d');
      $sql ="SELECT s.idservicios as idservicios,s.nombre  as nombre,s.fechaservicio as fechaservicio ,s.plazas as plazas, s.plazasocupadas, (s.plazas - s.plazasocupadas) as plazasdisponibles
      from reservasBundle:Servicios as s
      where  CAST(s.fechaservicio as date) like '$date' or CAST(s.fechaservicio  as date) > '$date' group by s.fechaservicio";
      return $this->getEntityManager()->createQuery($sql)->getResult();
    }
    public function findByBeforeToday(){
      $date = new \Datetime();
      $date = $date->format('Y-m-d');
      $sql ="SELECT s.idservicios as idservicios ,s.nombre as nombre,s.fechaservicio as fechaservicio ,s.plazas as plazas, s.plazasocupadas, (s.plazas - s.plazasocupadas) as plazasdisponibles
      from reservasBundle:Servicios as s
      where  CAST(s.fechaservicio as date) like '$date' or CAST(s.fechaservicio  as date) <  '$date' group by s.fechaservicio";
      return $this->getEntityManager()->createQuery($sql)->getResult();
    }
}
 ?>
