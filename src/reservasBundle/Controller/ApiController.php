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
use reservasBundle\Entity\ReservasHasAlergenos;
use Symfony\Component\Config\Definition\Exception\Exception;

class ApiController extends Controller
{
    public function sumarPlazas($reservas){
      $plazas = 0;
      foreach ($reservas as $key => $reserva) {
        $plazas += $reserva->getNpersonas();

      }
      return $plazas;
    }
    public function getPorcentajeReservas($servicio){
        $em = $this->getDoctrine()->getManager();
        $reservas = $em->getRepository("reservasBundle:Reservas")->findByServiciosservicios($servicio);
        $plazasOcupadas = self::sumarPlazas($reservas);
        $plazas = $servicio->getPlazas();
        $porcentaje = ($plazasOcupadas / $plazas) * 100;
        return $porcentaje;

    }
    public function isAlmostComplete($servicio){
      $em = $this->getDoctrine()->getManager();
      $config = $em->getRepository("reservasBundle:Config")->findAll()[0];

      if (self::getPorcentajeReservas($servicio)>=80 && $servicio->getAvisoenviado()==0) {
        // enviar email de aviso del ochenta porciento
        $message = new \Swift_Message("Servicio casi completo");
        $message->setTo($config->getEmailAdministrador());
        $message->setFrom("send@email.com");
        $message->setBody("texto para el mensaje");
        $this->get('mailer')->send($message);
        $servicio->setAvisoenviado(1);
      }
    }
    public function indexAction()
    {
      $array = array();
      $response = new JsonResponse($array);
      return $response;
    }
    public function plusPlazasOcupadas( $reserva){
      $em = $this->getDoctrine()->getEntityManager();
      $idestado = $reserva->getEstadoreservaestadoreserva()->getIdestadoreserva();
      if ($idestado == 2) {
        $servicio = $reserva->getServiciosservicios();
        $servicio->setPlazasocupadas($servicio->getPlazasocupadas() + $reserva->getNpersonas());
        $em->persist($servicio);
        $em->flush();
      }
    }
    public function lessPlazasOcupadas( $reserva){
      $em = $this->getDoctrine()->getEntityManager();
      $idestado = $reserva->getEstadoreservaestadoreserva()->getIdestadoreserva();
      if ($idestado == 2) {
        $servicio = $reserva->getServiciosservicios();
        $servicio->setPlazasocupadas($servicio->getPlazasocupadas() - $reserva->getNpersonas());
        $em->persist($servicio);
        $em->flush();
      }
    }
    //importante metodo interno no enrutar
    public function deleteAlergenosByReserva($reserva){
        $em=$this->getDoctrine()->getEntityManager();
        $alergenos  = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findByReservasreservas($reserva);
        foreach($alergenos as $alergeno){
          $em->remove($alergeno);
          $em->flush();
        }
        return true;
    }
    public function removeReserva($reserva){
      self::deleteAlergenosByReserva($reserva);
      $em = $this->getDoctrine()->getEntityManager();
      $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
      $message = new \Swift_Message('Se ha Anulado su reserva');
      $message->setTo($reserva->getCorreo());
      $message->setFrom('send@email.com');
      $message->setBody($config->getCancelacion());
      $this->get('mailer')->send($message);
      $em->persist($servicio);
      $em->remove($reserva);
      $em->flush();
      self::lessPlazasOcupadas($reserva);
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
    public function deletereservasAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($request->get('id'))[0];
      self::removeReserva($reserva);
      $response = new JsonResponse(true);
      return $response;

    }
    public function editreservasAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($request->get('id'))[0];
      self::deleteAlergenosByReserva($reserva);
      $reserva->setNombre($request->get('nombre'));
      $reserva->setApellidos($request->get('apellidos'));
      $reserva->setCorreo($request->get('correo'));
      $reserva->setTelefono($request->get('telefono'));
      $reserva->setObservaciones($request->get('observaciones'));
      $reserva->setHorallegada(new \DateTime($request->get('horallegada')));
      $reserva->setNpersonas($request->get('npersonas'));

      $em->persist($reserva);
      $em->flush();
      $alergenos = [];
      if(count($request->get('alergenos'))>0||!empty($request->get('alergenos'))){
        $alergenos = $request->get('alergenos');
      }
      foreach ($alergenos as $key => $ralergeno) {
        $alergeno = $em->getRepository('reservasBundle:Alergenos')->findByNombre($ralergeno);

        $reservashasalergenos = new ReservasHasAlergenos();
        $reservashasalergenos->setAlergenosalergenos($alergeno[0]);
        $reservashasalergenos->setReservasreservas($reserva);
        $em->persist($reservashasalergenos);
        $em->flush();
      }
      $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
      $message = new \Swift_Message('Se ha editado su reserva');
      $message->setTo($reserva->getCorreo());
      $message->setFrom('send@email.com');
      $message->setBody($config->getEdicionreserva());
      $this->get('mailer')->send($message);
      $response =  new JsonResponse(true);
      return $response;
    }
    public function addreservasAction(Request $request){
      try {
        $em = $this->getDoctrine()->getEntityManager();

        $estadoreserva = $em->getRepository('reservasBundle:Estadoreserva')->findByIdestadoreserva($request->get('estado'))[0];
        $servicio = $em->getRepository('reservasBundle:Servicios')->findByIdservicios($request->get('servicio'))[0];
        $reservas = new Reservas();
        $reservas->setNombre($request->get('nombre'));
        $reservas->setApellidos($request->get('apellidos'));
        $reservas->setCorreo($request->get('correo'));
        $reservas->setTelefono($request->get('telefono'));
        $reservas->setObservaciones($request->get('observaciones'));
        $reservas->setNpersonas($request->get('npersonas'));
        $reservas->setHorallegada(new \Datetime($request->get('horallegada')));
        $reservas->setServiciosservicios($servicio);
        $reservas->setEstadoreservaestadoreserva($estadoreserva);
        $codReserva = $servicio->getFechaServicio()->format('Ymdhis').$request->get('correo');
        $valcodreservas = $em->getRepository("reservasBundle:Reservas")->findByCodreserva($codReserva);
        if (count($valcodreservas)>0) {
          throw new Exception("Usted ya ha reservado  a este servicio");
        }
        $listanegra = $em->getRepository('reservasBundle:Listanegra')->findOneByCorreo($request->get('correo'));
        if (count($listanegra)>0) {
          $config = $em->getRepository('reservasBundle:Config')->findAll()[0];

          $message = new \Swift_Message('un usuario de la lista negra ha reservado');
          $message->setTo($config->getEmailAdministrador());
          $message->setFrom('send@email.com');
          $message->setBody($config->getListanegra());
          $this->get('mailer')->send($message);


        }
        $reservas->setCodreserva($codReserva);
        $em->persist($reservas);

        self::isAlmostComplete($servicio);
        if ($request->get('suscrito')== 1) {
          $correo = new Correos();
          $correo->setNombre($reservas->getNombre());
          $correo->setApellidos($reservas->getApellidos());
          $correo->setEmail($reservas->getCorreo());
          $em->persist($correo);
        }
        $em->flush();
        self::plusPlazasOcupadas($reservas);
        $alergenos = [];
        if(count($request->get('alergenos'))>0||!empty($request->get('alergenos'))){
          $alergenos = $request->get('alergenos');
        }
        foreach ($alergenos as $key => $ralergeno) {
          $alergeno = $em->getRepository('reservasBundle:Alergenos')->findByNombre($ralergeno);

          $reservashasalergenos = new ReservasHasAlergenos();
          $reservashasalergenos->setAlergenosalergenos($alergeno[0]);
          $reservashasalergenos->setReservasreservas($reservas);
          $em->persist($reservashasalergenos);
          $em->flush();
        }
        $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
        $message = new \Swift_Message('Se ha realizado su reserva');
        $message->setTo($reservas->getCorreo());
        $message->setFrom('send@email.com');
        $message->setBody($config->getConfirmacion());
        $this->get('mailer')->send($message);
        $response = new JsonResponse($reservas->toArray());
        return $response;
      } catch (Exception $e) {
          $response = new JsonResponse(array(
            'error'=>$e->getMessage(),
          ));
          return $response;
      }



    }

    public function plazasrestantesAction(Request $request){

        $date = new \Datetime($request->get('fecha'));

        $em = $this->getDoctrine()->getEntityManager();
        $plazas = $em->getRepository('reservasBundle:Servicios')->findById($request->get('id'));

        $response = new JsonResponse($plazas);
        return $response;
    }
    public function serviciosAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository('reservasBundle:Servicios')->findByToday();
      $auxservicios = array();
      $response = new JsonResponse($servicios);
      return $response;
    }
    public function serviciosbyidAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $servicio = $em->getRepository('reservasBundle:Servicios')->findByFechaservicio(new \DateTime($request->get('fecha')))[0];
      $response = new JsonResponse($servicio->toArray());
      return $response;
    }
    public function reservabycodreservaAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findByCodreserva($request->get('codigo'))[0];
      $response = new JsonResponse($reserva->toArray());
      return $response;
    }
    public function serviciosbyfechaAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $date =  new \Datetime($request->get('fecha'));

      $servicios = $em->getRepository('reservasBundle:Servicios')->findLikeFechaservicio($date);

      $response = new JsonResponse($servicios);
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
    public function alergenosbyreservaAction(Request $request){
      $em  = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository("reservasBundle:Reservas")->findByIdreservas($request->get("id"))[0];
      $alergenos = $em->getRepository("reservasBundle:ReservasHasAlergenos")->findByReservasreservas($reserva);
      $auxAlergenos = array();
      foreach($alergenos as $alergeno){
        $auxAlergenos[] = $alergeno->toArray();

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
