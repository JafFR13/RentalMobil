
<footer class="footer bg-dark text-white text-center py-3">
  <small>&copy; <?= date("Y"); ?> RentalMobil - Semua Hak Dilindungi</small>
</footer>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../css/aos/dist/aos.js"></script>
<script>
  AOS.init({
    once: true,
    duration: 800,
    offset: 100,
  });

    // Animasi sederhana: cards muncul dengan fade/zoom saat halaman selesai load
    document.addEventListener("DOMContentLoaded", function() {
    const cards = document.querySelectorAll(".card");
    cards.forEach((card, i) => {
      setTimeout(() => card.classList.add("show"), i * 150);
    });
  });
</script>
</body>
</html>