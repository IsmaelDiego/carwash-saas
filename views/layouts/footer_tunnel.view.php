            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
                <div class="container-fluid">
                    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            &copy;
                            <script>document.write(new Date().getFullYear());</script>
                            — <span class="fw-bold text-primary">Carwash XP</span> | Panel Operario
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
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<script src="<?= BASE_URL ?>/template/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?= BASE_URL ?>/template/assets/vendor/libs/popper/popper.js"></script>
<script src="<?= BASE_URL ?>/template/assets/vendor/js/bootstrap.js"></script>
<script src="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?= BASE_URL ?>/template/assets/vendor/js/menu.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= BASE_URL ?>/template/assets/js/main.js"></script>
<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2-clientes, .select2-ordenes-activas, select.form-select.select2').select2({
            width: '100%'
        });
        // Para selects que estén dentro de modales (como en modals.php)
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('select.select2, select.select2-clientes, select.select2-ordenes-activas').select2({
                dropdownParent: $(this),
                width: '100%'
            });
        });
    }
});
</script>
</body>
</html>
