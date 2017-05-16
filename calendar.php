<?php
    /**
     * Dado un mes y un aÃ±o, mostrar el calendario del mes.
     *
     * @author Estela MuÃ±oz CordÃ³n
     * @license Creative Commons, Attribution - Non commercial
     */

    // Zona horaria
    date_default_timezone_set('Europe/Madrid');
    // Variables
    //$anioActual = date('Y');
    $mes = $anio = $diasMes = $errorM = $errorA = "";

    // Dato obtenido - EJEMPLO
    $fecha = "1/2/2017";
    $mesInput = "2";
    $anioInput = "2017";
    $diaInput = "1";

    $mes = 5;
    $anio = 2017;

    // Cuántos días tiene el mes
    $diasMes = date('t', mktime(0, 0, 0, $mes, 1, $anio ));
    // Contador de semanas
    $semana = 1;

    // Array de las semanas de ese mes y aÃ±o
    for($i = 1; $i <= $diasMes; $i++) {
        $diaSemana = date('N', strtotime("$anio-$mes" . '-' . $i));

        $calendario[$semana][$diaSemana] = $i;

        if ($diaSemana == 7) {
            $semana++;
        }
    }

    // El calendario
    echo "<table id=\"calendario\">
    <thead>
        <tr id=\"trPeque\">
            <td><span>L</span></td>
            <td><span>M</span></td>
            <td><span>X</span></td>
            <td><span>J</span></td>
            <td><span>V</span></td>
            <td><span>S</span></td>
            <td><span>D</span></td>
        </tr>
        <tr id=\"trGrande\">
            <td><span>Lunes</span></td>
            <td><span>Martes</span></td>
            <td><span>Miércoles</span></td>
            <td><span>Jueves</span></td>
            <td><span>Viernes</span></td>
            <td><span>Sábado</span></td>
            <td><span>Domingo</span></td>
        </tr>
    </thead>
    <tbody>";

    for($i = 1; $i < 7; $i++){
        echo "<tr>";
        for ($j = 1; $j <= 7; $j++) {
            if (isset($calendario[$i][$j])) {
                if($j == 7) {
                    echo "<td bgcolor=\"LightCoral\">";
                }else{
                    echo "<td>";
                }
                echo $calendario[$i][$j];
                echo "</td>";
            }else{
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table> ";


?>
