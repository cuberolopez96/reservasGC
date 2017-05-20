<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use reservasBundle\Entity\Servicios;
use reservasBundle\Entity\Menu;
use reservasBundle\Entity\Correos;
use reservasBundle\Entity\Misplantillas;
use reservasBundle\Entity\Reservas;


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
    public function addreservasAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $servicio = $em->getRepository('reservasBundle:Servicios')->findByIdservicios($request->get('servicio'))[0];
      $reservas = new Reservas();
      $reservas->setNombre($request->get('nombre'));
      $reservas->setApellidos($request->get('apellidos'));
      $reservas->setCorreo($request->get('correo'));
      $reservas->setTelefono($request->get('telefono'));
      $reservas->setObservaciones($request->get('telefono'));
      $reservas->setNpersonas($request->get('npersonas'));
      $reservas->setServiciosservicios($servicio);
      $em->persist($reservas);
      $em->flush();
      $response = new JsonResponse(true);
      return $response;

    }
    public function plazasrestantesAction(Request $request){

        $date = new \Datetime($request->get('fecha'));

        $em = $this->getDoctrine()->getEntityManager();
        $plazas = $em->getRepository('reservasBundle:Reservas')->getPlazasOcupadas($date);
        $response = new JsonResponse($plazas);
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
    public function editserviciosAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $servicio = $em->getRepository("reservasBundle:Servicios")->findByIdservicios($request->get('id'))[0];
      $servicio->setFechaservicio(new \Datetime($request->get('fecha')));
      $servicio->setPlazas($request->get('plazas'));
      $em->persist($servicio);
      $em->flush();
      $response = new JsonResponse(true);
      return $response;
    }
    public function addserviciosAction(Request $request){

      $servicio = new Servicios();

      try {
        $servicio->setFechaservicio(new \DateTime($request->get('FechaServicio')));
        $servicio->setPlazas(intval($request->get('Plazas')));
        $em=$this->getDoctrine()->getEntityManager();
        $em->persist($servicio);
        $em->flush();
        $response = new JsonResponse(array());
        return $response;
      } catch (Exception $e) {
          $response = new JsonResponse(array('error'=>$e->getMessage()));
      }



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
    public function addmenuAction(Request $request){
      $menu = new Menu();
      try {
        $menu->setDescripción($request->get('Descripcion'));
        $menu->setImagen($request->get('Imagen'));
        $em=$this->getDoctrine()->getEntityManager();
        $em->persist($menu);
        $em->flush();
        $response = new JsonResponse(array());
        return $response;
      } catch (Exception $e) {
          $response = new JsonResponse(array('error'=>$e->getMessage()));
      }
    }
    public function editmenuAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $menu = $em->getRepository('reservasBundle:Menu')
      ->findByIdmenu($request->get('id'))[0];
      $menu->setDescripción($request->get('descripcion'));
      $menu->setImagen($request->get('imagen'));
      $em->persist($menu);
      $em->flush();
      $response = new JsonResponse(true);
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
    public function addcorreosAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $correo  = new Correos();
      $correo->setNombre($request->get('nombre'));
      $correo->setApellidos($request->get('apellidos'));
      $correo->setEmail($request->get('correos'));
      $em->persist($correo);
      $em->flush();
      $response = new JsonResponse(array('true'=>true));
      return $response;
    }
    public function editcorreosAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $correo = $em->getRepository('reservasBundle:Correos')
      ->findByIdcorreos($request->get('id'))[0];
      $correo->setNombre($request->get('nombre'));
      $correo->setApellidos($request->get('apellidos'));
      $correo->setEmail($request->get('correo'));
      $em->persist($correo);
      $em->flush();
      $response = new JsonResponse(true);
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
    public function addplantillasAction(Request $request){
      $em= $this->getDoctrine()->getEntityManager();
      $plantilla = new Misplantillas();
      $plantilla->setAsunto($request->get('asunto'));
      $plantilla->setTexto($request->get('texto'));
      $em->persist($plantilla);
      $em->flush();
      $response = new JsonResponse(array('estado'=>true));
      return $response;
    }
    public function editplantillasAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $plantilla = $em->getRepository('reservasBundle:Misplantillas')
      ->findByIdmisplantillas($request->get('id'))[0];
      $plantilla->setAsunto($request->get('asunto'));
      $plantilla->setTexto($request->get('texto'));
      $em->persist($plantilla);
      $em->flush();
      $response = new JsonResponse(true);
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
