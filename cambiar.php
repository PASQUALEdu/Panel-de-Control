<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambiar Contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Tus estilos -->
  <link rel="stylesheet" href="css/cambiar_contraseña.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/cards.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <!-- Contenedor para el Sidebar -->
  <div id="sidebar-container"></div>

  <!-- Main Content -->
  <div class="main-content">
    <h1><i class="fas fa-key"></i> Cambiar Contraseña</h1>
    <!-- Div para mostrar mensajes de éxito o error -->
    <div id="response-message"></div>
    
    <form id="change-password-form">
      <label for="nueva_contraseña">Nueva Contraseña</label>
      <div class="password-container">
        <input type="password" id="nueva_contraseña" name="nueva_contraseña" required>
        <i class="toggle-password fas fa-eye"></i>
      </div>

      <label for="confirmar_contraseña">Confirmar Contraseña</label>
      <div class="password-container">
        <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required>
        <i class="toggle-password fas fa-eye"></i>
      </div>

      <button type="submit">Cambiar Contraseña</button>
    </form>
  </div>

  <script>
    // Cargar el sidebar desde sidebar.html (asegúrate de que sea un fragmento sin <html> etc.)
    fetch('sidebar.php')
      .then(response => response.text())
      .then(data => {
        document.getElementById('sidebar-container').innerHTML = data;
      })
      .catch(error => console.error('Error al cargar el sidebar:', error));

    // Manejar el toggle de las contraseñas
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('toggle-password')) {
        const input = e.target.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        e.target.classList.toggle('fa-eye');
        e.target.classList.toggle('fa-eye-slash');
      }
    });

    // Enviar el formulario de cambio de contraseña vía AJAX
    document.getElementById('change-password-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('php/changePassword.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        const responseDiv = document.getElementById('response-message');
        if (data.error) {
          responseDiv.innerHTML = '<p class="error"><i class="fas fa-exclamation-circle"></i> ' + data.error + '</p>';
        } else if (data.mensaje) {
          responseDiv.innerHTML = '<p class="success"><i class="fas fa-check-circle"></i> ' + data.mensaje + '</p>';
        }
      })
      .catch(error => console.error('Error al enviar el formulario:', error));
    });
  </script>
  <!-- Si tu sidebar.js es necesario para la funcionalidad extra, puedes cargarlo aquí -->
  <script src="js/sidebar.js"></script>
</body>
</html>
