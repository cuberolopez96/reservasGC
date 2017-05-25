<?php

namespace reservasBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use reservasBundle\Form\ConfigType;
use reservasBundle\Entity\Config;
class AdminController extends Controller
{
    public function indexAction()
    {
      return $this->render('reservasBundle:Admin:index.html.twig');
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
