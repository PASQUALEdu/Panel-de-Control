<?php
date_default_timezone_set('America/Mexico_City');

// Inicializar cURL y obtener los contratos desde la API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://erp.plazashoppingcenter.store/htdocs/api/index.php/contracts",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['DOLAPIKEY: web123456789']
]);

$result = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

// Si ocurre error en la petición, lo mostramos
if ($result === false) {
    echo "<div class='error'>Error al obtener datos: " . $error . "</div>";
    exit;
}

// Decodificar el JSON recibido
$contracts = json_decode($result, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<div class='error'>Error al decodificar JSON</div>";
    exit;
}

// Generar el HTML de las tarjetas de contrato
foreach ($contracts as $data) {
    // Verificar que haya al menos 1 servicio abierto en el contrato
    if ($data["nbofservicesopened"] >= 1) {
        $recentLine = null;
        // Buscar la línea más reciente del contrato
        foreach ($data["lines"] as $line) {
            $dateStart = DateTime::createFromFormat('U', $line["date_start"]);
            $dateEnd = DateTime::createFromFormat('U', $line["date_end"]);
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

        // Calcular días restantes
        $hoy = new DateTime();
        $finContrato = clone $recentLine['dateEnd'];
        $finContrato->setTime(0, 0);
        $hoy->setTime(0, 0);
        $diferencia = $hoy->diff($finContrato);
        $diasRestantes = $diferencia->days * ($diferencia->invert ? -1 : 1);

        // Determinar estado y la clase CSS según los días restantes
        if ($diasRestantes < 0) {
            $estado = "VENCIDO HACE " . abs($diasRestantes) . " DÍAS";
            $estadoClass = "no-pagado";
        } elseif ($diasRestantes == 0) {
            $estado = "VENCE HOY";
            $estadoClass = "pagar";
        } elseif ($diasRestantes == 1) {
            $estado = "VENCE MAÑANA";
            $estadoClass = "pagar";
        } else {
            $estado = "VIGENTE ($diasRestantes días restantes)";
            $estadoClass = "pagado";
        }

        // Imprimir la tarjeta del contrato
        echo "<div class='contract-card'>";
        echo "<h2><i class='fas fa-store'></i> {$data['ref_customer']}</h2>";
        echo "<p><i class='fas fa-hashtag'></i> Referencia: {$data['ref']}</p>";
        echo "<p><i class='fas fa-calendar-alt'></i> Inicio: " . $recentLine['dateStart']->format('d-m-Y') . "</p>";
        echo "<p><i class='fas fa-calendar-times'></i> Fin: " . $recentLine['dateEnd']->format('d-m-Y') . "</p>";
        echo "<p class='status $estadoClass'><i class='fas fa-info-circle'></i> $estado</p>";
        echo "</div>";
    }
}
?>
