<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Tu hoja de estilos personalizada -->
  <link rel="stylesheet" href="css/styles_inicio_sesion.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Inicio</title>
  <style>
    /* Estilos para el mensaje de error */
    .error-message {
      color: red;
      font-size: 14px;
      margin-top: 10px;
      display: none; /* Oculto por defecto */
    }
  </style>
</head>
<body>
  <div class="container" id="container">
    <div class="form-container sign-in">
      <form id="loginForm" action="iniciar_sesion.php" method="POST" onsubmit="return validateCredentials()">
        <h1>INICIAR SESIÓN</h1>
        <hr>
        <?php if (isset($_GET['error'])): ?>
          <p class="error"><?php echo $_GET['error']; ?></p>
        <?php endif; ?>

        <i class="fa-solid fa-user"></i>
        <label for="usuario">Usuario</label>
        <input type="text" name="usuario" id="usuario" placeholder="Nombre de usuario" required>

        <i class="fa-solid fa-unlock"></i>
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" placeholder="Contraseña" required>
        
        <!-- Mensaje de error -->
        <p id="errorMessage" class="error-message">Datos incorrectos, intente de nuevo</p>
        
        <hr>
        <button type="submit">Iniciar Sesión</button>
      </form>
    </div>
    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-right">
          <h1>Bienvenido</h1>
          <p>Arrendatario</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    function validateCredentials() {
      // Obtener los valores de los campos de usuario y contraseña
      const usuario = document.getElementById('usuario').value;
      const password = document.getElementById('password').value;

      // Credenciales correctas
      const correctUsuario = 'admin';
      const correctPassword = '456';

      // Validar las credenciales
      if (usuario !== correctUsuario || password !== correctPassword) {
        // Mostrar el mensaje de error
        document.getElementById('errorMessage').style.display = 'block';
        return false; // Evita que el formulario se envíe
      }

      // Ocultar el mensaje de error si las credenciales son correctas
      document.getElementById('errorMessage').style.display = 'none';
      return true; // Permite que el formulario se envíe
    }
  </script>
</body>
</html>