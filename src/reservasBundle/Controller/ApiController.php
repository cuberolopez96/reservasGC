<?php

namespace reservasBundle\Controller;

use reservasBundle\Entity\Alergenos;
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
      /** 
       * @var  $key
       * @var  Reservas $reserva
       */
        foreach ($reservas as $key => $reserva) {
        $plazas += $reserva->getNpersonas();

      }
      return $plazas;
    }

    /**
     * @param Servicios $servicio
     * @return float|int
     */
    public function getPorcentajeReservas($servicio){
        $em = $this->getDoctrine()->getManager();
        $reservas = $em->getRepository("reservasBundle:Reservas")->findBy(array('serviciosservicios'=>$servicio));//findByServiciosservicios($servicio);
        $plazasOcupadas = self::sumarPlazas($reservas);
        $plazas = $servicio->getPlazas();
        $porcentaje = ($plazasOcupadas / $plazas) * 100;
        return $porcentaje;

    }

    /**
     * @param Servicios $servicio
     */
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

    /**
     * @param Reservas $reserva
     */
    public function plusPlazasOcupadas($reserva){
      $em = $this->getDoctrine()->getManager();
      $idestado = $reserva->getEstadoreservaestadoreserva()->getIdestadoreserva();
      if ($idestado == 2) {
        $servicio = $reserva->getServiciosservicios();
        $servicio->setPlazasocupadas($servicio->getPlazasocupadas() + $reserva->getNpersonas());
        $em->persist($servicio);
        $em->flush();
      }
    }

    /**
     * @param Reservas $reserva
     */
    public function lessPlazasOcupadas($reserva){
      $em = $this->getDoctrine()->getManager();
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
        $em=$this->getDoctrine()->getManager();
        $alergenos  = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findBy(array('reservasreservas'=>$reserva));//findByReservasreservas($reserva);
        foreach($alergenos as $alergeno){
          $em->remove($alergeno);
          $em->flush();
        }
        return true;
    }

    /**
     * @param Reservas $reserva
     */
    public function removeReserva($reserva){
      self::deleteAlergenosByReserva($reserva);
      $em = $this->getDoctrine()->getManager();
      $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
      $message = new \Swift_Message('Se ha Anulado su reserva');
      $message->setTo($reserva->getCorreo());
      $message->setFrom('send@email.com');
      $message->setFrom($this->renderView('reservasBundle:Admin:correosReservas.html.twig'),'text/html');//$message->setBody($config->getCancelacion());
      $this->get('mailer')->send($message);
      //$em->persist($servicio);
      $em->remove($reserva);
      $em->flush();
      self::lessPlazasOcupadas($reserva);
    }

    public function reservasAction(){
      $em = $this->getDoctrine()->getManager();
      $reservas = $em->getRepository('reservasBundle:Reservas')->findAll();
      $auxreservas = array();
      foreach ($reservas as $key => $reserva) {
        $auxreservas[]= $reserva->toArray();
      }
      $response = new JsonResponse($auxreservas);
      return $response;
    }
    public function deletereservasAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findOneBy(array('idreservas'=>$request->get('id')));//findByIdreservas($request->get('id'))[0];
      self::removeReserva($reserva);
      $response = new JsonResponse(true);
      return $response;

    }
    public function editreservasAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findOneBy(array('idreservas'=>$request->get('id')));//findByIdreservas($request->get('id'))[0];
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
        $alergeno = $em->getRepository('reservasBundle:Alergenos')->findBy(array('nombre'=>$ralergeno));//findByNombre($ralergeno);

        $reservashasalergenos = new ReservasHasAlergenos();
        $reservashasalergenos->setAlergenosalergenos($alergeno);
        $reservashasalergenos->setReservasreservas($reserva);
        $em->persist($reservashasalergenos);
        $em->flush();
      }
      $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
      $message = new \Swift_Message('Se ha editado su reserva');
      $message->setTo($reserva->getCorreo());
      $message->setFrom('send@email.com');
      $message->setBody($this->renderView('reservasBundle:Admin:correosReservas.html.twig',array('reserva'=>$reserva,'message'=>$config->getEdicionReserva())),'text/html');//$message->setBody($config->getEdicionreserva());
      $this->get('mailer')->send($message);
      $response =  new JsonResponse(true);
      return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addreservasAction(Request $request){
      try {
        $em = $this->getDoctrine()->getManager();

        $estadoreserva = $em->getRepository('reservasBundle:Estadoreserva')->findOneBy(array('idestadoreserva'=>$request->get('estado')));//findByIdestadoreserva($request->get('estado'))[0];
        $servicio = $em->getRepository('reservasBundle:Servicios')->findOneBy(array('idservicios'=>$request->get('servicio')));//findByIdservicios($request->get('servicio'))[0];
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
        $valcodreservas = $em->getRepository("reservasBundle:Reservas")->findBy(array('codreserva'=>$codReserva));//findByCodreserva($codReserva);
        if (count($valcodreservas)>0) {
          throw new Exception("Usted ya ha reservado  a este servicio");
        }
        $listanegra = $em->getRepository('reservasBundle:Listanegra')->findAll();//findOneByCorreo($request->get('correo'));
        if (count($listanegra)>0) {
          $config = $em->getRepository('reservasBundle:Config')->findAll()[0];

          $message = new \Swift_Message('un usuario de la lista negra ha reservado');
          $message->setTo($config->getEmailAdministrador());
          $message->setFrom('send@email.com');
          //lista negra cambiar
          $message->setBody($this->renderView('reservasBundle:Admin:correosReservas.html.twig',array('reserva'=>$reservas)),'text/html');//$message->setBody($config->getListanegra());
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
        if(array_key_exists("alergenos",$request)||!empty($request->get('alergenos'))){
          $alergenos = $request->get('alergenos');
        }
        foreach ($alergenos as $key => $ralergeno) {
          $alergeno = $em->getRepository('reservasBundle:Alergenos')->findOneBy(array('nombre'=>$ralergeno));//findByNombre($ralergeno);

          $reservashasalergenos = new ReservasHasAlergenos();
          $reservashasalergenos->setAlergenosalergenos($alergeno);
          $reservashasalergenos->setReservasreservas($reservas);
          $em->persist($reservashasalergenos);
          $em->flush();
        }
        $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
        $message = new \Swift_Message('Se ha realizado su reserva');
        $message->setTo($reservas->getCorreo());
        $message->setFrom('send@email.com');
        $message->setBody($this->renderView("reservasBundle:Admin:correosReservas.html.twig",array('reserva'=>$reservas,'body'=>$config->getConfirmacion())),'text/html');
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

        /** @var \DateTime $date */
        $date = new \Datetime($request->get('fecha'));

        $em = $this->getDoctrine()->getManager();
        $plazas = $em->getRepository('reservasBundle:Servicios')->findById($request->get('id'));

        $response = new JsonResponse($plazas);
        return $response;
    }
    public function serviciosAction(){
      $em = $this->getDoctrine()->getManager();
      $servicios = $em->getRepository('reservasBundle:Servicios')->findByToday();
      $auxservicios = array();
      $response = new JsonResponse($servicios);
      return $response;
    }
    public function serviciosbyidAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $servicio = $em->getRepository('reservasBundle:Servicios')->findOneBy(array('fechaservicio'=>new \DateTime($request->get('fecha'))));//findByFechaservicio(new \DateTime($request->get('fecha')))[0];
      $response = new JsonResponse($servicio->toArray());
      return $response;
    }
    public function reservabycodreservaAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findOneBy(array('codreserva'=>$request->get('codigo')));//findByCodreserva($request->get('codigo'))[0];
      $response = new JsonResponse($reserva->toArray());
      return $response;
    }
    public function serviciosbyfechaAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $date =  new \Datetime($request->get('fecha'));

      $servicios = $em->getRepository('reservasBundle:Servicios')->findLikeFechaservicio($date);

      $response = new JsonResponse($servicios);
      return $response;
    }
    public function editserviciosAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $servicio = $em->getRepository("reservasBundle:Servicios")->findOneBy(array('idservicios'=>$request->get('id')));//findByIdservicios($request->get('id'))[0];
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
        $em=$this->getDoctrine()->getManager();
        $em->persist($servicio);
        $em->flush();
        $response = new JsonResponse(array());
        return $response;
      } catch (Exception $e) {
          $response = new JsonResponse(array('error'=>$e->getMessage()));
      }



    }
    public function listaNegraAction(){
      $em = $this->getDoctrine()->getManager();
      $listaNegra= $em->getRepository('reservasBundle:Listanegra')->findAll();
      $auxListaNegra = array();
      foreach($listaNegra as $item){
        $auxListaNegra[] = $item->toArray();
      }
      $response = new JsonResponse($auxListaNegra);
      return $response;
    }
    public function menuAction(){
      $em =  $this->getDoctrine()->getManager();
      $menus = $em->getRepository("reservasBundle:Menu")->findAll();
      $auxMenu = array();
      foreach($menus as $menu){
        $auxMenu[] = $menu->toArray();
      }
      $response = new JsonResponse($auxMenu);
      return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addmenuAction(Request $request){
      $menu = new Menu();
      try {
        $menu->setDescripción($request->get('Descripcion'));
        $menu->setImagen($request->get('Imagen'));
        $em=$this->getDoctrine()->getManager();
        $em->persist($menu);
        $em->flush();
          /** @var JsonResponse $response */
          $response = new JsonResponse(array());
        return $response;
      } catch (Exception $e) {
          $response = new JsonResponse(array('error'=>$e->getMessage()));
      }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editmenuAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $menu = $em->getRepository('reservasBundle:Menu')
      ->findOneBy(array('idmenu'=>$request->get('id')));//findByIdmenu($request->get('id'))[0];
      $menu->setDescripción($request->get('descripcion'));
      $menu->setImagen($request->get('imagen'));
      $em->persist($menu);
      $em->flush();
      $response = new JsonResponse(true);
      return $response;
    }
    public function alergenosAction(){
      $em = $this->getDoctrine()->getManager();
      $alergenos = $em->getRepository("reservasBundle:Alergenos")->findAll();
      $auxAlergenos = array();
      foreach($alergenos as  $alergeno){
        $auxAlergenos[]= $alergeno->toArray();

      }
      $response = new JsonResponse($auxAlergenos);
      return $response;
    }
    public function alergenosbyreservaAction(Request $request){
      $em  = $this->getDoctrine()->getManager();
      $reserva = $em->getRepository("reservasBundle:Reservas")->findOneBy(array('idreservas'=>$request->get("id")));//findByIdreservas($request->get("id"))[0];
      $alergenos = $em->getRepository("reservasBundle:ReservasHasAlergenos")->findBy(array('reservasreservas'=>$reserva));//findByReservasreservas($reserva);
      $auxAlergenos = array();
      /** @var Alergenos $alergeno */
        foreach($alergenos as $alergeno){
        $auxAlergenos[] = $alergeno->toArray();

      }

      $response = new JsonResponse($auxAlergenos);
      return $response;
    }
    public function addcorreosAction(Request $request){
      $em = $this->getDoctrine()->getManager();
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
      $em = $this->getDoctrine()->getManager();
      $correo = $em->getRepository('reservasBundle:Correos')
      ->findOneBy(array('idcorreos'=>$request->get('id')));//findByIdcorreos($request->get('id'))[0];
      $correo->setNombre($request->get('nombre'));
      $correo->setApellidos($request->get('apellidos'));
      $correo->setEmail($request->get('correo'));
      $em->persist($correo);
      $em->flush();
      $response = new JsonResponse(true);
      return $response;
    }
    public function correosAction(){
      $em = $this->getDoctrine()->getManager();
      $correos = $em->getRepository("reservasBundle:Correos")->findAll();
      $auxCorreos = array();
      foreach($correos as $correo){
        $auxCorreos[] = $correo->toArray();
      }
      $response = new JsonResponse($auxCorreos);
      return $response;
    }
    public function addplantillasAction(Request $request){
      $em= $this->getDoctrine()->getManager();
      $plantilla = new Misplantillas();
      $plantilla->setAsunto($request->get('asunto'));
      $plantilla->setTexto($request->get('texto'));
      $em->persist($plantilla);
      $em->flush();
      $response = new JsonResponse(array('estado'=>true));
      return $response;
    }
    public function editplantillasAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $plantilla = $em->getRepository('reservasBundle:Misplantillas')
      ->findOneBy(array('idmisplantillas'=>$request->get('id')));//findByIdmisplantillas($request->get('id'))[0];
      $plantilla->setAsunto($request->get('asunto'));
      $plantilla->setTexto($request->get('texto'));
      $em->persist($plantilla);
      $em->flush();
      $response = new JsonResponse(true);
      return $response;

    }
    public function misPlantillasAction(){
      $em = $this->getDoctrine()->getManager();
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
