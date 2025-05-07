<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="<?= base_url(); ?>assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>DivvLaury</title>

    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url(); ?>assets/img/favicon/logo_tanah_laut.png" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/apex-charts/apex-charts.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
    <link rel="stylesheet"
        href="<?= base_url(); ?>assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
    <link rel="stylesheet"
        href="<?= base_url(); ?>assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/flatpickr/flatpickr.css" />
    <!-- toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .agenda-item {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
    </style>


    <!-- Helpers -->
    <script src="<?= base_url(); ?>assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url(); ?>assets/js/config.js"></script>

    <!-- full calendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?= $this->include('templates/sidebar.php'); ?>
            <!-- Layout container -->
            <div class="layout-page">
                <?= $this->include('templates/navbar.php'); ?>
                <!-- Offcanvas chat -->

                <!-- Content wrapper -->
                <?= $this->renderSection('content'); ?>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?= base_url(); ?>assets/vendor/libs/jquery/jquery.js"></script>
    <!-- jquery cdn -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="<?= base_url(); ?>assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/js/bootstrap.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/js/menu.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/flatpickr/flatpickr.js"></script>
    <script>
        $(document).ready(function () {
            $('.flatpickr-date').flatpickr({
                dateFormat: 'Y-m-d',
            });
            $('.flatpickr-time').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
            });
        });
    </script>
    <!-- Vendors JS -->
    <script src="<?= base_url(); ?>assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script>
        $(document).ready(function () {
            $('.datatables-basic').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                }
            });
        });

    </script>
    <script src="<?= base_url(); ?>assets/js/ui-toasts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // toast error message
        <?php if (session()->getFlashdata('error')): ?>
            $(document).ready(function () {
                toastr.error('<?= session()->getFlashdata('error') ?>');
            });
        <?php endif; ?>
        // toast success message
        <?php if (session()->getFlashdata('success')): ?>
            $(document).ready(function () {
                toastr.success('<?= session()->getFlashdata('success') ?>');
            });
        <?php endif; ?>
    </script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function logout() {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda akan keluar dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/logout';
                }
            });
        }
    </script>

    <!-- Main JS -->
    <script src="<?= base_url(); ?>assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="<?= base_url(); ?>assets/js/dashboards-analytics.js"></script>
    <!-- Page JS -->
    <script src="<?= base_url(); ?>assets/js/tables-datatables-basic.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>