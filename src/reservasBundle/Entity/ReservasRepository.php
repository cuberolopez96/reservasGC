<?php
// src/AppBundle/Repository/ProductRepository.php
namespace reservasBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ReservasRepository extends EntityRepository
{
    public function getPlazasOcupadas($id)
    {
        $sql = "SELECT sum(r.npersonas) as POcupadas, s.idservicios, max(s.fechaservicio) as Fecha, max(s.plazas) as Plazas from reservasBundle:Reservas as r
inner join reservasBundle:Servicios as s with r.serviciosservicios = s.idservicios
where s.idservicios = '$id'
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
