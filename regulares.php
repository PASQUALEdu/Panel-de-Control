<?php
// regulares.php

// Inicializamos la solicitud cURL para obtener los contratos
$curl = curl_init();
$httpheader = ['DOLAPIKEY: web123456789'];
$url = "https://erp.plazashoppingcenter.store/htdocs/api/index.php/contracts";
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
$result = curl_exec($curl);

echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contratos - Clientes que ya pagaron</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>';

// Se llama al sidebar mediante include (igual que en home.php)
include 'sidebar.php';

echo '
<div class="main-content">
    <div class="header">
        <h1><i class="fas fa-chart-line"></i> Contratos - Clientes que ya pagaron</h1>
    </div>
    <div class="contracts-container">';

if ($result === false) {
    echo "<div class='error'>Error en la solicitud cURL: " . curl_error($curl) . "</div>";
} else {
    $contracts = json_decode($result, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        foreach ($contracts as $data) {
            // Verificamos que el contrato tenga servicios abiertos
            if ($data["nbofservicesopened"] >= 1) {
                // Obtenemos la línea más reciente del contrato
                $recentLine = null;
                foreach ($data["lines"] as $line) {
                    if ($recentLine === null || $line["date_start"] > $recentLine["date_start"]) {
                        $recentLine = $line;
                    }
                }
                if (!$recentLine) continue;

                // Si el contrato tiene servicio vencido, se omite (ya que no representa un pago completo)
                if ($data["nbofservicesexpired"] == 1) {
                    continue;
                }
                
                // Obtenemos el estado de pago: buscamos el campo "statut" o "status"
                $status = $recentLine["statut"] ?? $recentLine["status"] ?? null;
                
                // Solo mostramos contratos que ya pagaron (status == 4)
                if ($status == 4) {
                    // Calculamos los días restantes hasta la fecha de fin (date_end)
                    $currentTime = time();
                    $daysLeft = ceil(($recentLine["date_end"] - $currentTime) / 86400);
                    if ($daysLeft < 0) {
                        $daysLeft = 0;
                    }
                    
                    echo "<div class='contract-card'>";
                    
                    // Encabezado "Local No. X" (ajusta según tu formato)
                    echo "<h2><i class='fas fa-store'></i> Local No. " . $data["ref_customer"] . "</h2>";
                    
                    // Referencia
                    echo "<p><i class='fas fa-hashtag'></i> Referencia: " . $data["ref"] . "</p>";
                    
                    // Fecha de inicio
                    echo "<p><i class='fas fa-calendar-alt'></i> Inicio: " . date("d-m-Y", $recentLine["date_start"]) . "</p>";
                    
                    // Fecha de fin
                    echo "<p><i class='fas fa-calendar-times'></i> Fin: " . date("d-m-Y", $recentLine["date_end"]) . "</p>";
                    
                    // Estado de pago
                    echo "<p class='status pagado'><i class='fas fa-info-circle'></i> YA PAGÓ (" . $daysLeft . " días restantes)</p>";
                    
                    echo "</div>";
                }
            }
        }
    } else {
        echo "<div class='error'>Error al decodificar la respuesta JSON</div>";
    }
}

echo '      </div> <!-- Cierre del contenedor de cards -->
    </div> <!-- Cierre del main-content -->
</body>
</html>';

curl_close($curl);
?>
