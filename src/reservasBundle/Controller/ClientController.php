<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{
    public function indexAction()
    {
        return $this->render('reservasBundle:Client:index.html.twig');
    }

}

 ?>
