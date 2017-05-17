<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{
    public function indexAction()
    {
        return $this->render('reservasBundle:Client:index.html.twig');
    }
    public function reservasAction()
    {
        return $this->render('reservasBundle:Client:reservas.html.twig');
    }

    public function consultarAction()
    {
        return $this->render('reservasBundle:Client:consultar.html.twig');
    }
}

 ?>
