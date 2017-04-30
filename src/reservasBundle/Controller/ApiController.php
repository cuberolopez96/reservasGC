<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    public function indexAction()
    {
      $em = $this->getDoctrine()->getEntityManager();
      $alergenos = $em->getRepository('reservasBundle:Alergenos')->findAll();
      $auxalergenos = array();
      foreach ($alergenos as $key => $alergeno) {
        $auxalergenos[]= $alergeno->toArray();
      }
      $response = new JsonResponse($auxalergenos);
      return $response ;
    }

    public function reservasAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $reservas = $em->getRepository('reservasBundle:Reservas')->findAll();
      $auxreservas = array();
      foreach ($reservas as $key => $reserva) {
        $auxreservas[]= $reserva->toArray();
      }
      $response = new JsonResponse($auxreservas);
      return $response;
    }
    public function serviciosAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository('reservasBundle:Servicios')->findAll();
      $auxservicios = array();
      foreach ($servicios as $key => $servicio) {
        $auxservicios[]=$servicio->toArray();
      }
      $response = new JsonResponse($auxservicios);
      return $response;
    }
}

 ?>
