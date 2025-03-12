<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <nav class="sidebar">
    <header>
      <div class="image-text">
        <span class="image">
          <img src="img/971.jpg" alt="Logo">
        </span>
       
      </div>
      <!-- Botón toggle (opcional, se puede usar en conjunto con hover) -->
      <button class="toggle-btn">
        <i class='bx bx-chevron-right toggle'></i>
      </button>
    </header>
    <div class="menu-bar">
      <div class="menu">
        <ul class="menu-links">
          <li class="nav-link">
            <a href="home.php">
              <i class='bx bx-home-alt icon'></i>
              <span class="nav-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-link">
            <a href="regulares.php">
              <i class='bx bx-bar-chart-alt-2 icon'></i>
              <span class="nav-text">Reguales</span>
            </a>
          </li>
          <li class="nav-link">
            <a href="irreguales.php">
              <i class='bx bx-bell icon'></i>
              <span class="nav-text">Irreguales</span>
            </a>
          </li>
          <li class="nav-link">
            <a href="mensajes.php">
              <i class='bx bx-pie-chart-alt icon'></i>
              <span class="nav-text">Mandar</span>
            </a>
          </li>
          <li class="nav-link">
            <a href="cambiar.php">
              <i class='bx bx-pie-chart-alt icon'></i>
              <span class="nav-text">Cambiar contrasena</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="bottom-content">  
          <a href="cerrar_sesion.php">
            <i class='bx bx-log-out icon'></i>
            <span class="nav-text">Cerrar Sesion</span>
          </a>
      </div>
    </div>
  </nav>
  <script src="js/sidebar.js"></script>
</body>
</html>
