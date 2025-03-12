<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Contratos Regulares</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Estilos generales y de componentes -->
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/cards.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <!-- Iconos (FontAwesome y Boxicons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <!-- Contenedor del Sidebar (opcional, si deseas incluirlo) -->
  <div id="sidebar-container"></div>

  <!-- Contenido principal -->
  <div class="main-content">
    <h1 class="text">Contratos Regulares</h1>
    <!-- Contenedor para las tarjetas de contratos regulares -->
    <div class="contracts-container" id="contracts-container">
      <!-- Aquí se cargarán las tarjetas generadas por getContractsR.php -->
    </div>
  </div>

  <!-- Scripts -->
  <script src="js/sidebar.js"></script>
  <script>
    // Cargar el sidebar (si se utiliza)
    fetch('sidebar.php')
      .then(response => response.text())
      .then(htmlSidebar => {
        document.getElementById('sidebar-container').innerHTML = htmlSidebar;
      })
      .catch(error => console.error('Error al cargar el sidebar:', error));
  </script>
  <script>
    // Cargar las tarjetas de contratos regulares desde getContractsR.php
    fetch('php/getContractsR.php')
      .then(response => response.text())
      .then(htmlContracts => {
        document.getElementById('contracts-container').innerHTML = htmlContracts;
      })
      .catch(error => console.error('Error al cargar contratos regulares:', error));
  </script>
</body>
</html>