<?php

namespace reservasBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use reservasBundle\Form\ConfigType;
use reservasBundle\Entity\Config;
use reservasBundle\Form\ReservasType;
use reservasBundle\Form\ServiciosType;
class AdminController extends Controller
{
    public function indexAction()
    {
      return $this->render('reservasBundle:Admin:index.html.twig');
    }
    public function editreservasAction($id, Request $request){
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
            $reserva->setEstadoreservaestadoreserva($estadoreserva);
            $em->persist($reserva);
            $em->flush();
          }
      }
      return $this->render('reservasBundle:Admin:editreserva.html.twig',array(
        'reserva'=>$reserva,
        'estadosreserva'=>$estadosreserva
      ));
    }
    public function deletereservasAction($id){
      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($id)[0];
      $em->remove($reserva);
      $em->flush();
      return $this->redirect('/admin/reservas');

    }
    public function reservasAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $reservas = $em->getRepository('reservasBundle:Reservas')->findAll();
      $alergenos = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findAll();
      return $this->render('reservasBundle:Admin:reservas.html.twig',array('reservas'=>$reservas,
      'alergenos'=>$alergenos
      ));
    }
    public function deleteserviciosAction($id){
      $em = $this->getDoctrine()->getEntityManager();

      $servicio = $em->getRepository("reservasBundle:Servicios")
      ->findByIdservicios($id)[0];
      $em->remove($servicio);
      $em->flush();
      return $this->redirect('/admin/servicios');
    }
    public function editserviciosAction($id, Request $request ){
      $em = $this->getDoctrine()->getEntityManager();
      $servicio = $em->getRepository("reservasBundle:Servicios")
      ->findByIdservicios($id)[0];
      $form = $this->createForm(ServiciosType::class,$servicio);
      if ($request->isMethod('POST')) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($servicio);
            $em->flush();


        }


    }
    return $this->render('reservasBundle:Admin:editservicio.html.twig',array(
      'form'=>$form->createView()));
    }
    public function serviciosAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $servicios = $em->getRepository("reservasBundle:Servicios")->findAll();
      return $this->render("reservasBundle:Admin:servicios.html.twig",array('servicios'=>$servicios));
    }
    public function configAction(Request $request){
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
