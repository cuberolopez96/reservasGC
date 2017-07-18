<?php

namespace reservasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function indexAction()
    {
        return $this->render('reservasBundle:Client:index.html.twig');
    }
    public function pdfreservasAction($id){
      $em = $this->getDoctrine()->getEntityManager();
      $reserva = $em->getRepository('reservasBundle:Reservas')->findByIdreservas($id)[0];

      $pdf = new \FPDF();

        $pdf->AddPage();

        $pdf->Image('bundles/reservas/img/image948-e1447362808491.png', 10, 8, 20);
        $pdf->Cell(25);

        $pdf->SetFont('Arial','B',20);
        $pdf->Cell(60,20,utf8_decode('Reserva Restaurante-Escuela IES Gran Capitán '));
        $pdf->ln();
        $pdf->ln();


        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(60,10,utf8_decode('Fecha del Servicio: '));
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$reserva->getServiciosservicios()->getFechaservicio()->format('d-m-Y H:i:s'));
        $pdf->ln();

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(60,10,utf8_decode('Código de Reserva: '));
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode($reserva->getCodreserva()));
        $pdf->ln();

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(30,10,'Nombre: ');
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode($reserva->getNombre()));
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(30,10,'Apellidos: ');
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode($reserva->getApellidos()));
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(30,10,'Correos: ');
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode($reserva->getCorreo()));
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(30,10,utf8_decode('Teléfono: '));
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$reserva->getTelefono());
        $pdf->ln();

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(30,10,utf8_decode('Alérgenos: '));
        $alergenos = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findByReservasreservas($reserva);
        $str  = [];
        foreach($alergenos as $alergeno){
          $str[] = $alergeno->getAlergenosalergenos()->getNombre();

        }
        $str = join(', ', $str);
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode($str));
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Observaciones: ');
        $pdf->ln();
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode($reserva->getObservaciones()));
        $pdf->ln();
        $pdf->ln();

        $pdf->SetFont('Arial','B',20);
        $pdf->Cell(30,10,utf8_decode('Datos de Contacto - IES Gran Capitán: '));
        $pdf->ln();

        $pdf->SetFont('Arial','B',20);
        $pdf->Cell(40,10,utf8_decode('Dirección: '));
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(60,10,utf8_decode('C/ Arcos de la Frontera s/n CP 14014 Córdoba '));
        $pdf->ln();

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(30,10,utf8_decode('Teléfono: '));
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode('697 957 465'));
        $pdf->ln();

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(25,10,utf8_decode('E-mail: '));
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,utf8_decode('hosteleria@iesgrancapitan.org'));
        $pdf->ln();


        return new Response($pdf->Output(), 200, array(
            'Content-Type' => 'application/pdf'));
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
