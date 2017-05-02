<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class AdminController extends Controller
{
    public function indexAction()
    {
      return $this->render('reservasBundle:Admin:index.html.twig');
    }
    public function reservasAction()
    {
      return $this->render('reservasBundle:Admin:reservas.html.twig');
    }
    public function reservaAction( $id )
    {
      return $this->render('reservasBundle:Admin:reserva.html.twig');
    }
    public function addreservasAction()
    {
      return $this->render('reservasBundle:Admin:addreservas.html.twig');
    }
    public function editreservasAction()
    {
      return $this->render('reservasBundle:Admin:editreservas.html.twig');
    }
    public function serviciosAction(){
      return $this->render('reservasBundle:Admin:servicios.html.twig');
    }
    public function servicioAction($id){
      return $this->render('reservasBundle:Admin:servicio.html.twig');
    }

}

 ?>
