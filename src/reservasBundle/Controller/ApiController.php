<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    public function indexAction()
    {
      $array = array();
      $response = new JsonResponse($array);
      return $response;
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
    public function listaNegraAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $listaNegra= $em->getRepository('reservasBundle:Listanegra')->findAll();
      $auxListaNegra = array();
      foreach($listaNegra as $item){
        $auxListaNegra[] = $item->toArray();
      }
      $response = new JsonResponse($auxListaNegra);
      return $response;
    }
    public function menuAction(){
      $em =  $this->getDoctrine()->getEntityManager();
      $menus = $em->getRepository("reservasBundle:Menu")->findAll();
      $auxMenu = array();
      foreach($menus as $menu){
        $auxMenu[] = $menu->toArray();
      }
      $response = new JsonResponse($auxMenu);
      return $response;
    }
    public function alergenosAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $alergenos = $em->getRepository("reservasBundle:Alergenos")->findAll();
      $auxAlergenos = array();
      foreach($alergenos as  $alergeno){
        $auxAlergenos[]= $alergeno->toArray();

      }
      $response = new JsonResponse($auxAlergenos);
      return $response;
    }
    public function correosAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $correos = $em->getRepository("reservasBundle:Correos")->findAll();
      $auxCorreos = array();
      foreach($correos as $correo){
        $auxCorreos[] = $correo->toArray();
      }
      $response = new JsonResponse($auxCorreos);
      return $response;
    }
    public function misPlantillasAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $plantillas = $em->getRepository("reservasBundle:Misplantillas")->findAll();
      $auxPlantillas = array();
      foreach($plantillas as $plantilla){
        $auxPlantillas[] = $plantilla->toArray();
      }
      $response = new JsonResponse($auxPlantillas);
      return $response;
    }
}

 ?>
