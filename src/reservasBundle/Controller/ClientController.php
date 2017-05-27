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
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Nombre: ');
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$reserva->getNombre());
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Apellidos: ');
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$reserva->getApellidos());
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Correos: ');
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$reserva->getCorreo());
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Telefono: ');
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$reserva->getTelefono());
        $pdf->ln();

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Alergenos: ');
        $alergenos = $em->getRepository('reservasBundle:ReservasHasAlergenos')->findByReservasreservas($reserva);
        $str  = [];
        foreach($alergenos as $alergeno){
          $str[] = $alergeno->getAlergenosalergenos()->getNombre();

        }
        $str = join(', ', $str);
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$str);
        $pdf->ln();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Observaciones: ');
        $pdf->ln();
        $pdf->SetFont('Arial','',16);
        $pdf->Cell(40,10,$reserva->getObservaciones());
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
