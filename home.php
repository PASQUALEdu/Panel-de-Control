<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Home</title>
  <!-- Estilos generales y de componentes -->
  <link rel="stylesheet" href="css/styles.css">
  <!-- Estilos del sidebar -->
  <link rel="stylesheet" href="css/sidebar.css">
  <!-- Iconos (FontAwesome) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Boxicons -->
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
  
</head>
<body>
  <!-- Contenedor para el Sidebar -->
  <div id="sidebar-container"></div>
  
  <!-- Contenido principal -->
  <div class="main-content">
    <div class="contracts-container" id="contracts-container">
      <!-- Aquí se cargará el contenido generado por getContracts.php o similar -->
    </div>
  </div>
  
  <script>
    // Cargar el sidebar desde sidebar.html
    fetch('sidebar.php')
      .then(response => response.text())
      .then(data => {
        document.getElementById('sidebar-container').innerHTML = data;
      })
      .catch(error => console.error('Error al cargar el sidebar:', error));
      
    // Cargar las tarjetas de contratos desde getContracts.php (si es necesario)
    fetch('php/getContracts.php')
      .then(response => response.text())
      .then(data => {
        document.getElementById('contracts-container').innerHTML = data;
      })
      .catch(error => console.error('Error al cargar contratos:', error));
  </script>
</body>
</html>
