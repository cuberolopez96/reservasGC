<?php
// src/AppBundle/Repository/ProductRepository.php
namespace reservasBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ReservasRepository extends EntityRepository
{
    public function getPlazasOcupadas($date)
    {
        $dia = $date->format('Y-m-d');
        $sql = "SELECT sum(r.npersonas) as POcupadas, s.idservicios, max(s.fechaservicio) as Fecha, max(s.plazas) as Plazas from reservasBundle:Reservas as r
inner join reservasBundle:Servicios as s with r.serviciosservicios = s.idservicios
where CAST(s.fechaservicio as date) = '$dia'
group by s.idservicios";
//print_r($sql);
        return $this->getEntityManager()
            ->createQuery(
                $sql
            )
            ->getResult();
    }
}
 ?>
