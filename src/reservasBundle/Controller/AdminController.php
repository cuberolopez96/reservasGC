<?php

namespace reservasBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use reservasBundle\Form\ConfigType;
use reservasBundle\Entity\Config;
use reservasBundle\Entity\ReservasHasAlergenos;
use reservasBundle\Entity\MenuHasAlergenos;
use reservasBundle\Entity\Servicios;
use reservasBundle\Entity\Menu;
use reservasBundle\Entity\Usuario;
use reservasBundle\Entity\Correos;
use reservasBundle\Entity\Listanegra;
use reservasBundle\Entity\Misplantillas;
use reservasBundle\Form\ReservasType;
use reservasBundle\Form\ServiciosType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends Controller
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
      $plazasOcupadas = self::sumarPlazas($servicio);
      $plazas = $servicio->getPlazas();
      $porcentaje = ($plazasOcupadas / $plazas) * 100;
      return $porcentaje;

  }
  public function isAlmostComplete($servicio){
    if (self::getPorcentajeReservas($servicio)<80) {
      $servicio->setAvisoenviado(0);
    }
  }
  public function arraytoentity($servicioarray){

    $em =  $this->getDoctrine()->getManager();
    $servicioEntity = $em->getRepository("reservasBundle:Servicios")->findOneByIdservicios($servicioarray["idservicios"]);
    return $servicioEntity;
  }
  public function logoutAction()
  {
    // cierre de sesion
  }
  public function deleteserviciosanterioresAction(){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $servicios = $em->getRepository('reservasBundle:Servicios')->findByBeforeToday();

    foreach ($servicios as $key => $servicio) {
      self::removeServicio(self::arraytoentity($servicio));
    }
    return $this->redirectToRoute("reservas_admin_servicios_anteriores");
  }
  public function plusPlazasOcupadas( $reserva){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

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
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $idestado = $reserva->getEstadoreservaestadoreserva()->getIdestadoreserva();
    if ($idestado == 2) {
      $servicio = $reserva->getServiciosservicios();
      $servicio->setPlazasocupadas($servicio->getPlazasocupadas() - $reserva->getNpersonas());
      $em->persist($servicio);
      $em->flush();
    }
  }
  public function sendBoletinToOrders($reservas, \Swift_Message $message){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    foreach ($reservas as $key => $reserva) {
      print_r($reserva->getCorreo());
      $message->setTo($reserva->getCorreo());
      $this->get('mailer')->send($message);
    }
  }
  public function sendBoletinToConfirmedOrders($reservas, \Swift_Message $message){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    foreach ($reservas as $key => $reserva) {
      if ($reserva->getEstadoreservaestadoreserva()->getIdestadoreserva()==2) {
        $message->setTo($reserva->getCorreo());
        $this->get('mailer')->send($message);
      }
    }
  }
  public function removeReserva($reserva){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    self::deleteAlergenosByReserva($reserva);
    $em= $this->getDoctrine()->getEntityManager();
    $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
    $message = new \Swift_Message('Se ha eliminado su reserva');
    $message->setTo($reserva->getCorreo());
    $message->setFrom('send@email.com');
    $message->setBody($config->getCancelacion());
    $this->get('mailer')->send($message);
    $em->remove($reserva);
    $em->flush();
    self::lessPlazasOcupadas($reserva);
  }
  public function addlistanegrabyreservaAction($id){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $listanegra = new Listanegra();
    $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($id)[0];
    $listanegra->setCorreo($reserva->getCorreo());
    $em->persist($listanegra);
    $em->flush();
    $servicio = $reserva->getServiciosservicios();
    return $this->redirect('/admin/servicios/'.$servicio->getIdservicios().'/reservas');
  }
  public function confirmreservasAction($id){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($id)[0];
    $estadoreserva = $em->getRepository('reservasBundle:estadoreserva')->findByIdestadoreserva(2)[0];
    $reserva->setEstadoreservaestadoreserva($estadoreserva);
    $servicio = $reserva->getServiciosservicios();
    $em->persist($reserva);
    $em->flush();
    self::plusPlazasOcupadas($reserva);
    return $this->redirect('/admin/servicios/'.$servicio->getIdservicios().'/reservas');
  }
  public function resendreservasAction($id){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
    $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($id)[0];
    $message = new \Swift_Message('Plazas Disponibles Restaurante');
    $message->setTo($reserva->getCorreo());
    $message->setFrom('send@email.com');
    $message->setBody($config->getClistaespera());
    $this->get('mailer')->send($message);
    return $this->render('reservasBundle:Admin:emailcompletado.html.twig');

  }
  public function removeServicio($servicio){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em= $this->getDoctrine()->getEntityManager();
    $reservas = $em->getRepository('reservasBundle:Reservas')->findByServiciosservicios($servicio);
    foreach ($reservas as $key => $reserva) {
      self::removeReserva($reserva);
    }
    $em->remove($servicio);
    $em->flush();
  }
  public function loginAction(Request $request){
      $authUtils = $this->get('security.authentication_utils');
    // get the login error if there is one
      $error = $authUtils->getLastAuthenticationError();

      // last username entered by the user
      $lastUsername = $authUtils->getLastUsername();
      return $this->render("reservasBundle:Admin:login.html.twig");
  }
  public function deletelistanegraAction($id){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $listanegra = $em->getRepository('reservasBundle:Listanegra')
    ->findByIdlistanegra($id)[0];
    $em->remove($listanegra);
    $em->flush();
    return $this->redirect('/admin/listados');
  }
  public function addlistanegraAction(Request $request){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    if ($request->isMethod('POST')) {
      if ($request->get('add')) {
        $listanegra = new Listanegra();
        $listanegra->setCorreo($request->get('correo'));
        $em->persist($listanegra);
        $em->flush();

        return $this->redirect('/admin/listados');
      }
    }
    return $this->render('reservasBundle:Admin:addlistanegra.html.twig');
  }
  public function listadosAction(){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $listanegra = $em->getRepository('reservasBundle:Listanegra')->findAll();
      return $this->render('reservasBundle:Admin:listados.html.twig', array(
      'listanegra'=>$listanegra,

    ));
  }
  //importante metodo interno no enrutar
  public function deleteAlergenosByReserva($reserva){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em=$this->getDoctrine()->getEntityManager();
      $alergenos  = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findByReservasreservas($reserva);
      foreach( $alergenos as $alergeno){
        $em->remove($alergeno);
        $em->flush();
      }
      return true;
  }
  public function sendboletinAction(Request $request, $id){

    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $plantilla = $em->getRepository('reservasBundle:Misplantillas')
    ->findByIdmisplantillas($id)[0];
    $correos = $em->getRepository('reservasBundle:Correos')->findAll();
    if ($request->isMethod('POST')) {
      if ($request->get('send') && $request->get('correos')) {
        foreach ($request->get('correos') as $key => $correo) {
          $mail = new \Swift_Message($plantilla->getAsunto());
          $mail->setTo($correo)
          ->setFrom('send@email.es')
          ->setBody($this->renderView('reservasBundle:Admin:correosBoletines.html.twig',array('boletin'=>$plantilla)),'text/html');
          $this->get('mailer')->send($mail);
        }
        return $this->redirect('/admin/boletin/completado');
      }
    }
    return $this->render('reservasBundle:Admin:sendboletin.html.twig',array(
      'plantilla'=>$plantilla,
      'correos'=>$correos
    ));
  }
  public function deletecorreosAction($id){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $correo = $em->getRepository('reservasBundle:Correos')
    ->findByIdcorreos($id)[0];
    $em->remove($correo);
    $em->flush();
    return $this->redirect('/admin/correos');


  }
  public function addcorreosAction(Request $request){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      if ($request->isMethod('POST')) {
        if ($request->get('add')) {
            $correo = new Correos();
            $correo->setNombre(trim($request->get('nombre')));
            $correo->setApellidos(trim($request->get('apellidos')));
            $correo->setEmail(trim($request->get('correo')));
            $em->persist($correo);
            $em->flush();
            return $this->redirect('/admin/correos');
        }
      }
      return $this->render('reservasBundle:Admin:addcorreos.html.twig');
  }
  public function correosAction(){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $correos = $em->getRepository('reservasBundle:Correos')->findAll();
    return $this->render('reservasBundle:Admin:correos.html.twig',array(
      'correos'=>$correos
    ));
  }
  public function deleteboletinAction($id){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $plantilla = $em->getRepository('reservasBundle:Misplantillas')
    ->findByIdmisplantillas($id)[0];
    $em->remove($plantilla);
    $em->flush();
    return $this->redirect('/admin/boletin');
  }
  public function sendboletincompletedAction(){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');


    return $this->render('reservasBundle:Admin:emailcompletado.html.twig');
  }
  public function editboletinAction(Request $request, $id){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    $plantilla = $em->getRepository('reservasBundle:Misplantillas')->findByIdmisplantillas($id)[0];
    if ($request->isMethod('POST')) {
      if ($request->get('edit')) {
          $plantilla->setAsunto($request->get('asunto'));
          $plantilla->setTexto($request->get('texto'));
          $em->persist($plantilla);
          $em->flush();
          return $this->redirect('/admin/boletin');
      }
    }
    return $this->render('reservasBundle:Admin:editboletin.html.twig',array(
      'plantilla'=>$plantilla
    ));
  }
  public function addboletinAction(Request $request){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em = $this->getDoctrine()->getEntityManager();
    if ($request->isMethod('POST')) {
      $plantilla = new Misplantillas();
      if ($request->get('add')) {
          $plantilla->setAsunto(trim($request->get('asunto')));
          $plantilla->setTexto(trim($request->get('texto')));
          $em->persist($plantilla);
          $em->flush();
          return $this->redirect('/admin/boletin');

      }
    }
    return $this->render('reservasBundle:Admin:addboletin.html.twig');
  }
  public function boletinAction(Request $request){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $plantillas = $em->getRepository('reservasBundle:Misplantillas')->findAll();
      $plantillas = array_reverse($plantillas);
      return $this->render('reservasBundle:Admin:boletin.html.twig',array(
        'plantillas'=>$plantillas
      ));
  }
  public function deleteAlergenosByMenu($menu){
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

    $em=$this->getDoctrine()->getEntityManager();
    $alergenos = $em->getRepository('reservasBundle:MenuHasAlergenos')->findByMenumenu($menu);
    foreach ($alergenos as $key => $alergeno) {
      $em->remove($alergeno);
      $em->flush();
    }
    return true;
  }
    public function launchrecordserviceAction($id){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $servicio = $em->getRepository('reservasBundle:Servicios')->findByIdservicios($id)[0];
      $reservas = $em->getRepository('reservasBundle:Reservas')->findByServiciosservicios($servicio);
      $message = new \Swift_Message('Recordatorio de Reserva');
      $message->setFrom('send@email.com');
      $message->setBody('Queda menos de un dia para hacer efectiva su reserva');
      self::sendBoletinToConfirmedOrders($reservas,$message);
      return $this->redirect('/admin/servicios/'.$servicio->getIdservicios().'/reservas');
    }
    public function indexAction()
    {

      return $this->redirect("/admin/servicios");
    }
    public function editreservasAction($id, Request $request){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository("reservasBundle:Reservas")->findByIdreservas($id)[0];
      $estadosreserva = $em->getRepository("reservasBundle:Estadoreserva")->findAll();
      $form= $this->createForm(ReservasType::class,$reserva);

      if ($request->isMethod('POST')) {
          if($request->get('guardar')){
            $reserva->setNombre(trim($request->get('nombre')));
            $reserva->setApellidos(trim($request->get('apellidos')));
            $reserva->setCorreo(trim($request->get('correo')));
            $reserva->setTelefono(trim($request->get('telefono')));
            $reserva->setObservaciones(trim($request->get('observaciones')));
            $estadoreserva = $em->getRepository('reservasBundle:Estadoreserva')
            ->findByIdestadoreserva($request->get("estado"))[0];
            self::deleteAlergenosByReserva($reserva);
            if($request->get('alergenos')){
              foreach ($request->get('alergenos') as $key => $alergeno){
                $alergeno = $em->getRepository('reservasBundle:Alergenos')
                ->findByNombre($alergeno)[0];
                $ralergeno = new ReservasHasAlergenos();
                $ralergeno->setAlergenosalergenos($alergeno);
                $ralergeno->setReservasreservas($reserva);
                $em->persist($ralergeno);
                $em->flush();
                return $this->redirect('/admin/servicios');
              }
            }
            $reserva->setEstadoreservaestadoreserva($estadoreserva);
            $em->persist($reserva);
            $em->flush();
          }
      }
      $alergenos = $em->getRepository('reservasBundle:Alergenos')->findAll();
      $myalergenos = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findByReservasreservas($reserva);
      return $this->render('reservasBundle:Admin:editreserva.html.twig',array(
        'reserva'=>$reserva,
        'estadosreserva'=>$estadosreserva,
        'alergenos'=> $alergenos,
        'myalergenos'=>$myalergenos
      ));
    }
    public function deletereservasAction($id){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($id)[0];
      self::removeReserva($reserva);
      return $this->redirect('/admin/servicios');

    }
    public function serviciosanterioresAction(){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository('reservasBundle:Servicios')->findByBeforeToday();
      $reservas = $em->getRepository('reservasBundle:Reservas')->findAll();
      return $this->render('reservasBundle:Admin:servicios.html.twig',array(
        'servicios'=>$servicios,
        'reservas'=>$reservas
    ));
    }
    public function deleteserviciosAction($id){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();

      $servicio = $em->getRepository("reservasBundle:Servicios")
      ->findByIdservicios($id)[0];
      self::removeServicio($servicio);
      return $this->redirect('/admin/servicios');
    }
    public function deletemenuAction(Request $request, $id){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $menu = $em->getRepository('reservasBundle:Menu')->findByIdmenu($id)[0];
      $servicios = $em->getRepository('reservasBundle:Servicios')->findByMenumenu($menu);
      foreach ($servicios as $key => $servicio) {
        $servicio->setMenumenu(null);
      }
      self::deleteAlergenosByMenu($menu);
      $em->remove($menu);
      $em->flush();
      return $this->redirect('/admin/menus');
    }
    public function editmenuAction(Request $request, $id){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $menu = $em->getRepository('reservasBundle:Menu')->findByIdmenu($id)[0];
      $alergenos = $em->getRepository('reservasBundle:Alergenos')->findAll();

      if ($request->isMethod('POST')) {
        if ($request->get('guardar')) {
          $file = $request->files->get('imagen');
          $status = array('status' => "success","fileUploaded" => false);

          if (!is_null($file) ) {
            # code...
            // If a file was uploaded
            if ($file->getClientOriginalExtension()=="jpg"||$file->getClientOriginalExtension()=="png") {
              // generate a random name for the file but keep the extension
              $filename = uniqid().".".$file->getClientOriginalExtension();
              $path = "imagenes/";
              $file->move($path,$filename); // move the file to a path
              $status = array('status' => "success","fileUploaded" => true);
              $menu->setImagen($path.$filename);
            }

          }
          $menu->setNombre(trim($request->get('nombre')));
          $menu->setDescripción(trim($request->get('descripcion')));
          $menu->setPrecio(trim($request->get('precio')));
          $em->persist($menu);
          $em->flush();
          if ($request->get('alergenos')) {
            self::deleteAlergenosByMenu($menu);
            foreach ($request->get('alergenos') as $key => $nalergeno) {
              $alergeno = $em->getRepository('reservasBundle:Alergenos')
              ->findByNombre($nalergeno)[0];
              $malergeno = new MenuHasAlergenos();
              $malergeno->setAlergenosalergenos($alergeno);
              $malergeno->setMenumenu($menu);
              $em->persist($malergeno);
              $em->flush();

            }
            return $this->redirect('/admin/menus');
          }

        }
      }
      $myalergenos = $em->getRepository('reservasBundle:MenuHasAlergenos')->findByMenumenu($menu);
      return $this->render('reservasBundle:Admin:editmenu.html.twig',array(
        'menu'=>$menu,
        'alergenos'=>$alergenos,
        'myalergenos'=>$myalergenos
      ));

    }
    public function addmenuAction(Request $request){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $menu = new Menu();
      $alergenos = $em->getRepository('reservasBundle:Alergenos')->findAll();
      if ($request->isMethod('POST')) {
        $file = $request->files->get('imagen');
        $status = array('status' => "success","fileUploaded" => false);

        if (!is_null($file) ) {
          # code...
          // If a file was uploaded
          if ($file->getClientOriginalExtension()=="jpg"||$file->getClientOriginalExtension()=="png") {
            // generate a random name for the file but keep the extension
            $filename = uniqid().".".$file->getClientOriginalExtension();
            $path = "imagenes/";
            $file->move($path,$filename); // move the file to a path
            $status = array('status' => "success","fileUploaded" => true);
            $menu->setImagen($path.$filename);
          }

        }
        $menu->setNombre(trim($request->get('nombre')));
        $menu->setDescripción(trim($request->get('descripcion')));
        $menu->setPrecio(trim($request->get('precio')));
        $em->persist($menu);
        $em->flush();
        if ($request->get('alergenos')) {
          foreach ($request->get('alergenos') as $key => $nalergeno) {
            $alergeno = $em->getRepository('reservasBundle:Alergenos')->findByNombre($nalergeno)[0];
            $malergeno = new MenuHasAlergenos();
            $malergeno->setAlergenosalergenos($alergeno);
            $malergeno->setMenumenu($menu);
            $em->persist($malergeno);
            $em->flush();
          }
        }

        return $this->redirect('/admin/menus');
      }
      return $this->render('reservasBundle:Admin:addmenu.html.twig',
      array('alergenos'=>$alergenos));
    }
    public  function menusAction(){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $menus = $em->getRepository('reservasBundle:Menu')->findAll();
      return $this->render('reservasBundle:Admin:menus.html.twig',array(
        'menus' => $menus
      ));
    }
    public function profilemenuAction($id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getEntityManager();
        $menu = $em->getRepository('reservasBundle:Menu')->findByIdmenu($id)[0];
        $alergenos = $em->getRepository('reservasBundle:MenuHasAlergenos')->findByMenumenu($menu);
        return $this->render('reservasBundle:Admin:menu.html.twig',array(
          'menu'=>$menu,
          'alergenos'=>$alergenos
        ));

    }
    public function reservasserviciosAction($id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getEntityManager();
        $servicio = $em->getRepository('reservasBundle:Servicios')->findByIdservicios($id)[0];
        $reservas= $em->getRepository('reservasBundle:Reservas')->findByServiciosservicios($servicio);
        $alergenos = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findAll();
        $listanegra = $em->getRepository('reservasBundle:Listanegra')->findAll();
        return $this->render('reservasBundle:Admin:reservas.html.twig',array(
          'reservas'=>$reservas,
          'alergenos'=>$alergenos,
          'servicio'=>$servicio,
          'blacklist'=>$listanegra
        ));
    }
    public function addserviciosAction(Request $request){

      $em = $this->getDoctrine()->getEntityManager();
      $menus = $em->getRepository('reservasBundle:Menu')->findAll();
      if ($request->isMethod('POST')) {
        if($request->get('guardar')){
          if ($request->get('menu') != "null") {
            $menu = $em->getRepository('reservasBundle:Menu')
            ->findByIdmenu($request->get('menu'))[0];
          }else{
            $menu = null;
          }
          $servicio = new Servicios();
          $servicio->setMenumenu($menu);
          $fecha = $request->get('fecha').' '.$request->get('hora');
          $fecha = str_replace('/','-',$fecha);
          $servicio->setNombre(trim($request->get('nombre')));
          $servicio->setFechaservicio(new \DateTime($fecha));
          $servicio->setPlazas($request->get('plazas'));
          $servicio->setAvisoenviado(false);
          $em->persist($servicio);
          $em->flush();
          return $this->redirect('/admin/servicios');
        }
      }
      return $this->render('reservasBundle:Admin:addservicio.html.twig',
        array('menus'=>$menus)
      );
    }
    public function editserviciosAction($id, Request $request ){

      $em = $this->getDoctrine()->getEntityManager();
      $servicio = $em->getRepository("reservasBundle:Servicios")
      ->findByIdservicios($id)[0];
      $menus = $em->getRepository('reservasBundle:Menu')->findAll();
      $reservas = $em->getRepository('reservasBundle:Reservas')->findByServiciosservicios($servicio);
      if ($request->isMethod('POST')) {
        if($request->get('guardar')){
            $fecha = $request->get('fecha').' '.$request->get('hora');
            $fecha = str_replace('/','-',$fecha);
            $menu = $em->getRepository('reservasBundle:Menu')
            ->findByIdmenu(trim($request->get('menu')))[0];
            $servicio->setNombre(trim($request->get('nombre')));
            $servicio->setMenumenu($menu);
            $servicio->setFechaservicio(new \DateTime($fecha));
            $servicio->setPlazas(trim($request->get('plazas')));
            self::isAlmostComplete($servicio);
            $em->persist($servicio);
            $em->flush();
            $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
            $message = new \Swift_Message('Cambio en El servicio');
            $message->setFrom('send@email.com');
            $message->setBody($config->getEdicionservicio());
            self::sendBoletinToOrders($reservas,$message);
            return $this->redirect('/admin/servicios');
        }


      }
    return $this->render('reservasBundle:Admin:editservicio.html.twig',array(
      'servicio'=> $servicio,
      'menus'=>$menus

    ));
    }
    public function serviciosAction(){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository("reservasBundle:Servicios")->findByToday();
      $reservas =  $em->getRepository("reservasBundle:Reservas")->findAll();
      return $this->render("reservasBundle:Admin:servicios.html.twig",array(
        'servicios'=>$servicios,
        'reservas'=>$reservas
      ));
    }
    public function configAction(Request $request){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      $em=$this->getDoctrine()->getEntityManager();
      $configs = $em->getRepository('reservasBundle:Config')->findAll();
      $config = $configs[0];
      if ($request->isMethod('POST')) {

        if ($request->get('guardar')) {
          $config->setConfirmacion(trim($request->get('confirmacion')));
          $config->setRecordatorio(trim($request->get('recordatorio')));
          $config->setCancelacion(trim($request->get('cancelacion')));
          $config->setListanegra(trim($request->get('listanegra')));
          $config->setEdicionservicio(trim($request->get('edicions')));
          $config->setEdicionreserva(trim($request->get('edicionr')));
          $config->setClistaespera(trim($request->get('clistaespera')));
          $config->setEmailAdministrador(trim($request->get('email')));
          $config->setFirmaAdministrador(trim($request->get('firma')));
          $em->persist($config);
          $em->flush();
          return $this->redirect('/admin/config');
        }

    }
      return $this->render('reservasBundle:Admin:config.html.twig',array(
        'config'=>$config
      ));

    }

    public function manualAction(){

      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      return $this->render("reservasBundle:Admin:manual.html.twig");
    }

    public function correosplantillaAction(){
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

      return $this->render("reservasBundle:Admin:correosReservas.html.twig");
    }

}

 ?>
