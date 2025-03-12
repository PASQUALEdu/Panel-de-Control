document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.querySelector('.toggle-btn');
    const sidebar = document.querySelector('.sidebar');
    
    toggleBtn.addEventListener('click', () => {
      // Alternativamente, podrías usar click para forzar el estado "open"
      sidebar.classList.toggle('open');
    });
  });
  