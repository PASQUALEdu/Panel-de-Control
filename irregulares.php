<?php
date_default_timezone_set('America/Mexico_City');

// Función para enviar mensajes de WhatsApp
function enviarWhatsApp($token, $to, $body) {
    $params = array(
        'token' => $token,
        'to'    => $to,
        'body'  => $body
    );

    $ultramsgCurl = curl_init();
    curl_setopt_array($ultramsgCurl, array(
        CURLOPT_URL => "https://api.ultramsg.com/instance106245/messages/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($ultramsgCurl);
    $err = curl_error($ultramsgCurl);
    curl_close($ultramsgCurl);

    return [$err, $response];
}

// Procesar el envío manual si se recibe un formulario
$sendResponse = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contract_line_id'])) {
    $telefono = $_POST['telefono'] ?? '';
    $mensaje  = $_POST['mensaje'] ?? '';
    if (!empty($telefono) && !empty($mensaje)) {
        list($err, $response) = enviarWhatsApp('ekcyr2opsuz1wo6z', $telefono, $mensaje);
        if (!$err) {
            $sendResponse = "<div class='success-msg'><i class='fas fa-check-circle'></i> Mensaje enviado!</div>";
        } else {
            $sendResponse = "<div class='error-msg'><i class='fas fa-exclamation-triangle'></i> Error al enviar: $err</div>";
        }
    } else {
        $sendResponse = "<div class='error-msg'><i class='fas fa-phone-slash'></i> Datos insuficientes para enviar el mensaje.</div>";
    }
}

// Obtener los contratos desde la API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://erp.plazashoppingcenter.store/htdocs/api/index.php/contracts",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['DOLAPIKEY: web123456789']
]);

$result = curl_exec($curl);
curl_close($curl);

echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Contratos Irregulares</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .contract-card {
            background: white;
            padding: 20px;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }
        .pagado { background: #4CAF50; }
        .pagar { background: #FF5722; }
        .no-pagado { background: #F44336; }
        .desconocido { background: #9E9E9E; }
        .error-msg { color: #D32F2F; margin-top: 5px; }
        .success-msg { color: #388E3C; margin-top: 5px; }
        .info-msg { color: #1976D2; margin-top: 5px; }
        button {
            padding: 8px 15px;
            border: none;
            background: #1976D2;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: #155a9c; }
    </style>
</head>
<body>';

// Se llama al sidebar mediante include (igual que en home.php)
include 'sidebar.php';

echo '
<div class="main-content">
    <div class="header">
        <h1><i class="fas fa-chart-line"></i> Contratos Irregulares</h1>
    </div>';

// Mostrar mensaje de respuesta del envío si existe
if ($sendResponse) {
    echo $sendResponse;
}

echo '<div class="contracts-container">';

if ($result === false) {
    echo "<div class='error'>Error al obtener datos.</div>";
} else {
    $contracts = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "<div class='error'>Error al decodificar JSON</div>";
    } else {
        foreach ($contracts as $data) {
            // Solo consideramos contratos con servicios abiertos
            if ($data["nbofservicesopened"] < 1) continue;
            
            // Buscar la línea más reciente del contrato
            $recentLine = null;
            foreach ($data["lines"] as $line) {
                $dateStart = DateTime::createFromFormat('U', $line["date_start"]);
                $dateEnd   = DateTime::createFromFormat('U', $line["date_end"]);
                if ($dateStart && $dateEnd) {
                    if (!$recentLine || $dateStart > $recentLine["dateStart"]) {
                        $recentLine = [
                            'id'        => $line["id"],
                            'dateStart' => $dateStart,
                            'dateEnd'   => $dateEnd
                        ];
                    }
                }
            }
            if (!$recentLine) continue;
            
            // Calcular los días restantes
            $hoy = new DateTime();
            $finContrato = clone $recentLine['dateEnd'];
            $finContrato->setTime(0, 0);
            $hoy->setTime(0, 0);
            
            $diferencia = $hoy->diff($finContrato);
            $diasRestantes = $diferencia->days * ($diferencia->invert ? -1 : 1);
            
            // Filtrar: mostrar solo si el contrato está vencido (<=0) o vence mañana (==1)
            if (!($diasRestantes <= 0 || $diasRestantes == 1)) continue;
            
            // Definir el mensaje según los días restantes
            $mensaje = "";
            if ($diasRestantes == 1) {
                $mensaje = "Estimado cliente del {$data['ref_customer']}, le recordamos que su renta vence el día de mañana.";
            } elseif ($diasRestantes <= 0) {
                $mensaje = "Estimado cliente del {$data['ref_customer']}, le recordamos que su renta está vencida. Por favor, pase a pagar. Gracias.";
            }
            
            // Definir el estado y la clase para mostrarlo
            $estado = "";
            $estadoClass = "desconocido";
            if ($diasRestantes < 0) {
                $estado = "VENCIDO HACE " . abs($diasRestantes) . " DÍAS";
                $estadoClass = "no-pagado";
            } elseif ($diasRestantes == 0) {
                $estado = "VENCIDO HOY";
                $estadoClass = "pagar";
            } elseif ($diasRestantes == 1) {
                $estado = "VENCE MAÑANA";
                $estadoClass = "pagar";
            }
            
            echo "<div class='contract-card'>";
            echo "<h2><i class='fas fa-store'></i> {$data['ref_customer']}</h2>";
            echo "<p><i class='fas fa-hashtag'></i> Referencia: {$data['ref']}</p>";
            echo "<p><i class='fas fa-calendar-alt'></i> Inicio: " . $recentLine['dateStart']->format('d-m-Y') . "</p>";
            echo "<p><i class='fas fa-calendar-times'></i> Fin: " . $recentLine['dateEnd']->format('d-m-Y') . "</p>";
            echo "<p class='status $estadoClass'><i class='fas fa-info-circle'></i> $estado</p>";
            
            // Formulario para enviar el mensaje manualmente
            echo "<form method='post' style='margin-top: 10px;'>";
            echo "<input type='hidden' name='contract_line_id' value='{$recentLine['id']}'>";
            echo "<input type='hidden' name='telefono' value='" . ($data["array_options"]["options_numero_de_telefono_"] ?? '') . "'>";
            echo "<input type='hidden' name='mensaje' value='$mensaje'>";
            echo "<button type='submit'>Enviar Mensaje</button>";
            echo "</form>";
            
            echo "</div>";
        }
    }
}

echo '</div></div></body></html>';
?>
