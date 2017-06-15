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

class AdminController extends Controller
{

  public function isAuthorized(){
    $session = new Session();
    $bandera = false;
    if ($session->get('user')) {
      $bandera = true;
    }
    return $bandera;
  }
  public function sendBoletinToOrders($reservas, \Swift_Message $message){
    foreach ($reservas as $key => $reserva) {
      print_r($reserva->getCorreo());
      $message->setTo($reserva->getCorreo());
      $this->get('mailer')->send($message);
    }
  }
  public function removeReserva($reserva){
    self::deleteAlergenosByReserva($reserva);
    $em= $this->getDoctrine()->getEntityManager();
    $config = $em->getRepository('reservasBundle:Config')->findAll()[0];
    $message = new \Swift_Message('Se ha eliminado su reserva');
    $message->setTo($reserva->getCorreo());
    $message->setFrom('send@email.com');
    $message->setBody($config->getCancelacion());
    $this->get('mailer')->send($message);
    $servicio = $reserva->setServiciosservicios();
    $servicio->setPlazasocupadas($servicio->getPlazasocupadas() - $reserva->getNpersonas());
    $em->remove($reserva);
    $em->flush();
  }
  public function removeServicio($servicio){
    $em= $this->getDoctrine()->getEntityManager();
    $reservas = $em->getRepository('reservasBundle:Reservas')->findByServiciosservicios($servicio);
    foreach ($reservas as $key => $reserva) {
      self::removeReserva($reserva);
    }
    $em->remove($servicio);
    $em->flush();
  }
  public function loginAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $errores = "";

      if (self::isAuthorized() == true) {
        return $this->redirect('/admin');
      }
      if ($request->isMethod('POST')) {
        if ($request->get('login')) {
          $usuario = $em->getRepository("reservasBundle:Usuarios")->findOneBy(array(
            "username"=>$request->get('username'),
            "password"=>md5($request->get('password'))
          ));
          if (count($usuario)>0) {
            $session = new Session();
            $session->set('user',$usuario);
            return $this->redirect("/admin");
          }else{
            $errores = "error de Autentificación";
          }

        }
      }
      return $this->render("reservasBundle:Admin:login.html.twig",array('errores'=>$errores));
  }
  public function deletelistanegraAction($id){
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
    $em = $this->getDoctrine()->getEntityManager();
    $listanegra = $em->getRepository('reservasBundle:Listanegra')
    ->findByIdlistanegra($id)[0];
    $em->remove($listanegra);
    $em->flush();
    return $this->redirect('/admin/listados');
  }
  public function addlistanegraAction(Request $request){
    if (self::isAuthorized() == false) {
      return $this->redirect('/login');
    }
    $em = $this->getDoctrine()->getEntityManager();
    if ($request->isMethod('POST')) {
      if ($request->get('add')) {
        $listanegra = new Listanegra();
        $listanegra->setCorreo($request->get('correo'));
        $em->persist($listanegra);
        $em->flush();

        $this->redirect('/admin/listados');
      }
    }
    return $this->render('reservasBundle:Admin:addlistanegra.html.twig');
  }
  public function listadosAction(){
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
    $em = $this->getDoctrine()->getEntityManager();
    $listanegra = $em->getRepository('reservasBundle:Listanegra')->findAll();
    $estadoreserva = $em->getRepository('reservasBundle:Estadoreserva')->findByIdestadoreserva(1)[0];
    $reservas = $em->getRepository('reservasBundle:Reservas')->findByEstadoreservaestadoreserva($estadoreserva);
    return $this->render('reservasBundle:Admin:listados.html.twig', array(
      'listanegra'=>$listanegra,
      'reservas'=>$reservas
    ));
  }
  //importante metodo interno no enrutar
  public function deleteAlergenosByReserva($reserva){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em=$this->getDoctrine()->getEntityManager();
      $alergenos  = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findByReservasreservas($reserva);
      foreach( $alergenos as $alergeno){
        $em->remove($alergeno);
        $em->flush();
      }
      return true;
  }
  public function sendboletinAction(Request $request, $id){

    if (self::isAuthorized()== false) {
      return $this->redirect('/admin/login');
    }
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
          ->setBody($plantilla->getTexto());
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
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
    $em = $this->getDoctrine()->getEntityManager();
    $correo = $em->getRepository('reservasBundle:Correos')
    ->findByIdcorreos($id)[0];
    $em->remove($correo);
    $em->flush();
    return $this->redirect('/admin/correos');


  }
  public function addcorreosAction(Request $request){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      if ($request->isMethod('POST')) {
        if ($request->get('add')) {
            $correo = new Correos();
            $correo->setNombre($request->get('nombre'));
            $correo->setApellidos($request->get('apellidos'));
            $correo->setEmail($request->get('correo'));
            $em->persist($correo);
            $em->flush();
            return $this->redirect('/admin/correos');
        }
      }
      return $this->render('reservasBundle:Admin:addcorreos.html.twig');
  }
  public function correosAction(){
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
    $em = $this->getDoctrine()->getEntityManager();
    $correos = $em->getRepository('reservasBundle:Correos')->findAll();
    return $this->render('reservasBundle:Admin:correos.html.twig',array(
      'correos'=>$correos
    ));
  }
  public function deleteboletinAction($id){
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
    $em = $this->getDoctrine()->getEntityManager();
    $plantilla = $em->getRepository('reservasBundle:Misplantillas')
    ->findByIdmisplantillas($id)[0];
    $em->remove($plantilla);
    $em->flush();
    return $this->redirect('/admin/boletin');
  }
  public function sendboletincompletedAction(){
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
    return $this->render('reservasBundle:Admin:emailcompletado.html.twig');
  }
  public function editboletinAction(Request $request, $id){
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
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
    if (self::isAuthorized()==false) {
      return $this->redirect('/admin/login');
    }
    $em = $this->getDoctrine()->getEntityManager();
    if ($request->isMethod('POST')) {
      $plantilla = new Misplantillas();
      if ($request->get('add')) {
          $plantilla->setAsunto($request->get('asunto'));
          $plantilla->setTexto($request->get('texto'));
          $em->persist($plantilla);
          $em->flush();
          return $this->redirect('/admin/boletin');

      }
    }
    return $this->render('reservasBundle:Admin:addboletin.html.twig');
  }
  public function boletinAction(Request $request){
      if (self::isAuthorized() == false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $plantillas = $em->getRepository('reservasBundle:Misplantillas')->findAll();

      return $this->render('reservasBundle:Admin:boletin.html.twig',array(
        'plantillas'=>$plantillas
      ));
  }
  public function deleteAlergenosByMenu($menu){
    if (self::isAuthorized() == false) {
      return $this->redirect('/admin/login');
    }
    $em=$this->getDoctrine()->getEntityManager();
    $alergenos = $em->getRepository('reservasBundle:MenuHasAlergenos')->findByMenumenu($menu);
    foreach ($alergenos as $key => $alergeno) {
      $em->remove($alergeno);
      $em->flush();
    }
    return true;
  }
    public function indexAction()
    {

      return $this->redirect("/admin/servicios");
    }
    public function editreservasAction($id, Request $request){
      if (self::isAuthorized() == false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository("reservasBundle:Reservas")->findByIdreservas($id)[0];
      $estadosreserva = $em->getRepository("reservasBundle:Estadoreserva")->findAll();
      $form= $this->createForm(ReservasType::class,$reserva);

      if ($request->isMethod('POST')) {
          if($request->get('guardar')){
            $reserva->setNombre($request->get('nombre'));
            $reserva->setApellidos($request->get('apellidos'));
            $reserva->setCorreo($request->get('correo'));
            $reserva->setTelefono($request->get('telefono'));
            $reserva->setObservaciones($request->get('observaciones'));
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
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($id)[0];
      self::removeReserva($reserva);
      return $this->redirect('/admin/servicios');

    }
    public function serviciosanterioresAction(){
      if (self::isAuthorized() == false) {
          return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository('reservasBundle:Servicios')->findByBeforeToday();
      return $this->render('reservasBundle:Admin:servicios.html.twig',array('servicios'=>$servicios));
    }
    public function deleteserviciosAction($id){
      if (self::isAuthorized() == false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();

      $servicio = $em->getRepository("reservasBundle:Servicios")
      ->findByIdservicios($id)[0];
      self::removeServicio($servicio);
      return $this->redirect('/admin/servicios');
    }
    public function deletemenuAction(Request $request, $id){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $menu = $em->getRepository('reservasBundle:Menu')->findByIdmenu($id)[0];
      $em->remove($menu);
      $em->flush();
      return $this->redirect('/admin/menus');
    }
    public function editmenuAction(Request $request, $id){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $menu = $em->getRepository('reservasBundle:Menu')->findByIdmenu($id)[0];
      $alergenos = $em->getRepository('reservasBundle:Alergenos')->findAll();

      if ($request->isMethod('POST')) {
        if ($request->get('guardar')) {
          $menu->setDescripción($request->get('descripcion'));
          $menu->setPrecio($request->get('precio'));
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
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $menu = new Menu();
      $alergenos = $em->getRepository('reservasBundle:Alergenos')->findAll();
      if ($request->isMethod('POST')) {
        $menu->setDescripción($request->get('descripcion'));
        $menu->setPrecio($request->get('precio'));
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
      $em = $this->getDoctrine()->getEntityManager();
      $menus = $em->getRepository('reservasBundle:Menu')->findAll();
      return $this->render('reservasBundle:Admin:menus.html.twig',array(
        'menus' => $menus
      ));
    }
    public function profilemenuAction($id){
        if (self::isAuthorized() == false) {
          return $this->redirect('/admin/login');
        }
        $em = $this->getDoctrine()->getEntityManager();
        $menu = $em->getRepository('reservasBundle:Menu')->findByIdmenu($id)[0];
        return $this->render('reservasBundle:Admin:menu.html.twig',array('menu'=>$menu));

    }
    public function reservasserviciosAction($id){
        if (self::isAuthorized()==false) {
          return $this->redirect('/admin/login');
        }
        $em = $this->getDoctrine()->getEntityManager();
        $servicio = $em->getRepository('reservasBundle:Servicios')->findByIdservicios($id)[0];
        $reservas= $em->getRepository('reservasBundle:Reservas')->findByServiciosservicios($servicio);
        $alergenos = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findAll();
        return $this->render('reservasBundle:Admin:reservas.html.twig',array(
          'reservas'=>$reservas,
          'alergenos'=>$alergenos,
          'servicio'=>$servicio
        ));
    }
    public function addserviciosAction(Request $request){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $menus = $em->getRepository('reservasBundle:Menu')->findAll();
      if ($request->isMethod('POST')) {
        if($request->get('guardar')){
          $menu = $em->getRepository('reservasBundle:Menu')
          ->findByIdmenu($request->get('menu'))[0];
          $servicio = new Servicios();
          $servicio->setMenumenu($menu);
          $fecha = $request->get('fecha').' '.$request->get('hora');
          $fecha = str_replace('/','-',$fecha);
          $servicio->setFechaservicio(new \DateTime($fecha));
          $servicio->setPlazas($request->get('plazas'));
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
      if (self::isAuthorized()==false) {
          return $this->redirect('/admin/login');
      }
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
            ->findByIdmenu($request->get('menu'))[0];
            $servicio->setMenumenu($menu);
            $servicio->setFechaservicio(new \DateTime($fecha));
            $servicio->setPlazas($request->get('plazas'));
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
      if (self::isAuthorized()==false) {
          return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository("reservasBundle:Servicios")->findByToday();
      $reservas =  $em->getRepository("reservasBundle:Reservas")->findAll();
      return $this->render("reservasBundle:Admin:servicios.html.twig",array(
        'servicios'=>$servicios,
        'reservas'=>$reservas
      ));
    }
    public function configAction(Request $request){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em=$this->getDoctrine()->getEntityManager();
      $configs = $em->getRepository('reservasBundle:Config')->findAll();
      $config = $configs[0];
      if ($request->isMethod('POST')) {

        if ($request->get('guardar')) {
          $config->setConfirmacion($request->get('confirmacion'));
          $config->setRecordatorio($request->get('recordatorio'));
          $config->setListanegra($request->get('listanegra'));
          $config->setCancelacion($request->get('cancelacion'));
          $config->setEdicionservicio($request->get('edicions'));
          $config->setEdicionreserva($request->get('edicionr'));
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
      if (self::isAuthorized()==false) {
          return $this->redirect('/admin/login');
      }

      return $this->render("reservasBundle:Admin:manual.html.twig");
    }


}

 ?>
