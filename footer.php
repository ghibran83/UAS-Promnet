            <!-- footer start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">(SIMBA) Sistem Peminjaman Barang Mahasiswa</a>, All Right Reserved. 
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- footer end -->
        
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="bootstrap/lib/chart/chart.min.js"></script>
    <script src="bootstrap/lib/easing/easing.min.js"></script>
    <script src="bootstrap/lib/waypoints/waypoints.min.js"></script>
    <script src="bootstrap/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="bootstrap/lib/tempusdominus/js/moment.min.js"></script>
    <script src="bootstrap/lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="bootstrap/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="bootstrap/js/main.js"></script>
    
    <!-- Calendar Icon Click Handler -->
    <script>
        $(document).ready(function() {
            // Pas icon kalender di-klik, trigger input date nya
            $('.input-group-text').on('click', function() {
                var dateInput = $(this).siblings('input[type="date"]');
                if (dateInput.length > 0) {
                    dateInput[0].showPicker(); // Buka popup kalender
                }
            });
        });
    </script>
</body>

</html>
