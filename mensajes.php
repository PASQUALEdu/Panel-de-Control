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

$sendResults = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    if (!empty($mensaje)) {
        // Obtener todos los contratos desde la API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://erp.plazashoppingcenter.store/htdocs/api/index.php/contracts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['DOLAPIKEY: web123456789']
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        $contracts = json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $sendResults .= "<div class='error-msg'>Error al decodificar los datos de contratos.</div>";
        } else {
            $sentCount = 0;
            $errorCount = 0;
            // Recorrer cada contrato y enviar el mensaje
            foreach ($contracts as $data) {
                // Opcional: Solo enviar a contratos activos
                if ($data["nbofservicesopened"] < 1) continue;

                $telefono = $data["array_options"]["options_numero_de_telefono_"] ?? '';
                if (!empty($telefono)) {
                    list($err, $response) = enviarWhatsApp('ekcyr2opsuz1wo6z', $telefono, $mensaje);
                    if (!$err) {
                        $sentCount++;
                    } else {
                        $errorCount++;
                    }
                }
            }
            $sendResults .= "<div class='success-msg'><i class='fas fa-check-circle'></i> Mensaje enviado a $sentCount arrendatarios.</div>";
            if ($errorCount > 0) {
                $sendResults .= "<div class='error-msg'><i class='fas fa-exclamation-triangle'></i> Ocurrieron errores al enviar a $errorCount arrendatarios.</div>";
            }
        }
    } else {
        $sendResults .= "<div class='error-msg'><i class='fas fa-phone-slash'></i> Por favor ingrese un mensaje.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensajes a Todos - Plaza Shopping Center</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
        /* Se omite la definición del sidebar ya que éste se incluye desde sidebar.php */
        .main-content {
            
            padding: 20px;
        }
        .header h1 { margin-top: 0; }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        button {
            margin-top: 10px;
            padding: 10px 20px;
            border: none;
            background: #1976D2;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: #155a9c; }
        .success-msg { color: #388E3C; margin-top: 10px; }
        .error-msg { color: #D32F2F; margin-top: 10px; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-envelope"></i> Enviar Mensajes a Todos</h1>
        </div>
        <?php
            if (!empty($sendResults)) {
                echo $sendResults;
            }
        ?>
        <div class="form-container">
            <form action="mensajes.php" method="post">
                <label for="mensaje"><strong>Escribe el mensaje para enviar a todos los arrendatarios:</strong></label>
                <textarea name="mensaje" id="mensaje" placeholder="Escribe aquí tu mensaje..."></textarea>
                <button type="submit">Enviar Mensaje a Todos</button>
            </form>
        </div>
    </div>
</body>
</html>
