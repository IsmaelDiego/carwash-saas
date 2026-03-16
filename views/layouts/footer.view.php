 <!-- Footer -->
 <footer class="content-footer footer bg-footer-theme">
     <div class="container-fluid">
         <div
             class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
             <div class="mb-2 mb-md-0">
                 &#169;
                 <script>
                     document.write(new Date().getFullYear());
                 </script>
                 , desarrolado por
                 <a href="https://themeselection.com" target="_blank" class="footer-link">Ismael Diego </a>
             </div>

         </div>
     </div>
 </footer>
 <!-- / Footer -->

 <div class="content-backdrop fade"></div>
 </div>
 <!-- Content wrapper -->
 </div>
 <!-- / Layout page -->
 </div>

 <!-- Overlay -->
 <div class="layout-overlay layout-menu-toggle"></div>
 </div>
 <!-- / Layout wrapper -->


 <!-- Core JS -->


 <script src="<?= BASE_URL ?>/template/assets/vendor/libs/popper/popper.js"></script>
 <script src="<?= BASE_URL ?>/template/assets/vendor/js/bootstrap.js"></script>

 <script src="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

 <script src="<?= BASE_URL ?>/template/assets/vendor/js/menu.js"></script>

 <!-- endbuild -->

 <!-- Vendors JS -->
 <script src="<?= BASE_URL ?>/template/assets/vendor/libs/apex-charts/apexcharts.js"></script>

 <!-- Main JS -->

 <script src="<?= BASE_URL ?>/template/assets/js/main.js"></script>

 <!-- Page JS -->
 <script src="<?= BASE_URL ?>/template/assets/js/dashboards-analytics.js"></script>

 <!-- Place this tag before closing body tag for github widget button. -->
 <script async defer src="https://buttons.github.io/buttons.js"></script>

 <!-- DATABALES JS -->
 <script src="<?= BASE_URL ?>/template/assets/js/dashboards-analytics.js"></script>

 <script src="<?= BASE_URL ?>/public/assets/vendor/datatables/js/datatables.js"></script>
 <script src="<?= BASE_URL ?>/public/assets/vendor/datatables/js/datatables.min.js"></script>



 <script>
     // Busca todos los elementos con la clase .hide-url
     document.querySelectorAll('.hide-url').forEach(link => {
         // 1. Guardamos la ruta real
         const url = link.getAttribute('href');

         // 2. Quitamos el href para que no salga abajo
         link.removeAttribute('href');

         // 3. Añadimos el cursor de mano (porque al quitar href se pierde)
         link.style.cursor = 'pointer';

         // 4. Añadimos el evento click
         link.addEventListener('click', () => {
             window.location.href = url;
         });
     });
 </script>
 </body>

 </html>