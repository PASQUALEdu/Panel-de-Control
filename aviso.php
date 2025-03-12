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

$sendResult = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['telefono']) && isset($_POST['mensaje'])) {
    $telefono = trim($_POST['telefono']);
    $mensaje  = trim($_POST['mensaje']);
    if (!empty($telefono) && !empty($mensaje)) {
        list($err, $response) = enviarWhatsApp('ekcyr2opsuz1wo6z', $telefono, $mensaje);
        if (!$err) {
            $sendResult = "<div class='success-msg'><i class='fas fa-check-circle'></i> Mensaje enviado a $telefono!</div>";
        } else {
            $sendResult = "<div class='error-msg'><i class='fas fa-exclamation-triangle'></i> Error al enviar: $err</div>";
        }
    } else {
        $sendResult = "<div class='error-msg'><i class='fas fa-phone-slash'></i> Seleccione un arrendatario y escriba un mensaje.</div>";
    }
}

// Obtener los contratos para listar arrendatarios
$tenants = [];
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://erp.plazashoppingcenter.store/htdocs/api/index.php/contracts",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['DOLAPIKEY: web123456789']
]);
$result = curl_exec($curl);
curl_close($curl);

if ($result !== false) {
    $contracts = json_decode($result, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        foreach ($contracts as $data) {
            // Consideramos sólo contratos activos y que tengan número de teléfono
            if ($data["nbofservicesopened"] < 1) continue;
            $telefono = $data["array_options"]["options_numero_de_telefono_"] ?? '';
            if (!empty($telefono)) {
                $tenants[] = [
                    'ref_customer' => $data['ref_customer'],
                    'ref'          => $data['ref'],
                    'telefono'     => $telefono
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensaje Individual - Plaza Shopping Center</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
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
        select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
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
            <h1><i class="fas fa-user"></i> Enviar Mensaje Individual</h1>
        </div>
        <?php if (!empty($sendResult)) { echo $sendResult; } ?>
        <div class="form-container">
            <form action="aviso.php" method="post">
                <label for="tenant">Selecciona un arrendatario:</label>
                <select name="telefono" id="tenant">
                    <option value="">-- Seleccione un arrendatario --</option>
                    <?php foreach ($tenants as $tenant): ?>
                        <option value="<?php echo htmlspecialchars($tenant['telefono']); ?>">
                            <?php echo htmlspecialchars($tenant['ref_customer'] . " (" . $tenant['ref'] . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="mensaje"><strong>Escribe el mensaje:</strong></label>
                <textarea name="mensaje" id="mensaje" placeholder="Escribe aquí tu mensaje..."></textarea>
                <button type="submit">Enviar Mensaje</button>
            </form>
        </div>
    </div>
</body>
</html>
