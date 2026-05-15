document.getElementById("toggleSidebar").addEventListener("click", function() {
    document.getElementById("wrapper").classList.toggle("toggled");
  });
  
  const modalKembali = document.getElementById('modalKembali');
modalKembali.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const idSewa = button.getAttribute('data-id');
  const namaMobil = button.getAttribute('data-mobil');
  
  modalKembali.querySelector('#idSewaModal').value = idSewa;
  modalKembali.querySelector('#namaMobilModal').textContent = namaMobil;
});