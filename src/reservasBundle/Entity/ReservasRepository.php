<?php
// src/AppBundle/Repository/ProductRepository.php
namespace reservasBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ReservasRepository extends EntityRepository
{
    public function getPlazasOcupadas($date)
    {
        return $this->getEntityManager()
            ->createQuery(
              "SELECT sum(r.NPersonas) as POcupadas, s.idServicios, max(s.FechaServicio) as Fecha from reservasgc.reservas as r
inner join reservasgc.servicios as s on r.Servicios_idServicios = s.idServicios
where cast(s.FechaServicio as date) = '$date'
group by s.idServicios;"
            )
            ->getResult();
    }
}
 ?>
