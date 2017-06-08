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
    return $this->render('reservasBundle:Admin:listados.html.twig', array(
      'listanegra'=>$listanegra
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
      $em->remove($reserva);
      $em->flush();
      return $this->redirect('/admin/reservas');

    }
    public function reservasAction(){
      if (self::isAuthorized() == false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $reservas = $em->getRepository('reservasBundle:Reservas')->findAll();
      $alergenos = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findAll();
      return $this->render('reservasBundle:Admin:reservas.html.twig',array('reservas'=>$reservas,
      'alergenos'=>$alergenos
      ));
    }
    public function deleteserviciosAction($id){
      if (self::isAuthorized() == false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();

      $servicio = $em->getRepository("reservasBundle:Servicios")
      ->findByIdservicios($id)[0];
      $em->remove($servicio);
      $em->flush();
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
      if (self::isAuthorized()) {
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
      if (self::isAuthorized()) {
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
    public function addserviciosAction(Request $request){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      if ($request->isMethod('POST')) {
        if($request->get('guardar')){
          $servicio = new Servicios();
          $fecha = $request->get('fecha').' '.$request->get('hora');
          $fecha = str_replace('/','-',$fecha);
          $servicio->setFechaservicio(new \DateTime($fecha));
          $servicio->setPlazas($request->get('plazas'));
          $em->persist($servicio);
          $em->flush();
          return $this->redirect('/admin/servicios');
        }
      }
      return $this->render('reservasBundle:Admin:addservicio.html.twig');
    }
    public function editserviciosAction($id, Request $request ){
      if (self::isAuthorized()==false) {
          return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $servicio = $em->getRepository("reservasBundle:Servicios")
      ->findByIdservicios($id)[0];
      $form = $this->createForm(ServiciosType::class,$servicio);
      if ($request->isMethod('POST')) {
        if($request->get('guardar')){
            $fecha = $request->get('fecha').' '.$request->get('hora');
            $fecha = str_replace('/','-',$fecha);
            $servicio->setFechaservicio(new \DateTime($fecha));
            $servicio->setPlazas($request->get('plazas'));
            $em->persist($servicio);
            $em->flush();
        }


      }
    return $this->render('reservasBundle:Admin:editservicio.html.twig',array(
      'servicio'=> $servicio,

    ));
    }
    public function serviciosAction(){
      if (self::isAuthorized()==false) {
          return $this->redirect('/admin/login');
      }
      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository("reservasBundle:Servicios")->findAll();
      return $this->render("reservasBundle:Admin:servicios.html.twig",array('servicios'=>$servicios));
    }
    public function configAction(Request $request){
      if (self::isAuthorized()==false) {
        return $this->redirect('/admin/login');
      }
      $em=$this->getDoctrine()->getEntityManager();
      $configs = $em->getRepository('reservasBundle:Config')->findAll();
      $config = $configs[0];
      $form = $this->createForm(ConfigType::class,$config);
      if ($request->isMethod('POST')) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($config);
            $em->flush();


        }
    }
      return $this->render('reservasBundle:Admin:config.html.twig',array(
        'form'=>$form->createView()
      ));

    }


}

 ?>
