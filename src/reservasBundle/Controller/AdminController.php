<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class Admin extends Controller
{
    public function indexAction()
    {
      return $this->render('reservasBundle:Default:index.html.twig');
    }
}

 ?>
