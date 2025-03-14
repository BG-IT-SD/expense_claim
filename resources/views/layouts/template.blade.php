<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  {{-- data-assets-path="template/assets/" --}}
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>EX</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="assets-path" content="{{ asset('template/assets/') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('template/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/materialdesignicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/node-waves/node-waves.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/demo.css') }}" />


    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
     {{-- datatable --}}
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
     {{-- /datatable --}}
     {{-- sweetalert2 --}}
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
     {{-- /sweetalert2 --}}
     {{-- datepicker --}}
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
     <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/pickr/pickr-themes.css') }}" />
     {{-- /datepicker --}}

     <link rel="stylesheet" href="{{ asset('template/assets/css/loading.css') }}" />

    <!-- Page CSS -->
    @yield('csscustom')

    <!-- Helpers -->
    <script src="{{ asset('template/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    {{-- <script src="template/assets/vendor/js/template-customizer.js"></script> --}}
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('template/assets/js/config.js') }}"></script>
  </head>

  <body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="spinner"></div>
    </div>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        @include('layouts.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          @include('layouts.header')

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            @yield('content')
            <!-- / Content -->

            <!-- Footer -->

            @include('layouts.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js -->
     {{-- <script src="template/assets/vendor/js/core.js"></script> --}}
    <script src="{{ asset('template/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    {{-- <script src="template/assets/vendor/libs/apex-charts/apexcharts.js"></script> --}}
    {{-- datatable --}}
    <script src="{{ asset('template/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

      {{-- /datatable --}}
    {{-- sweetalert2 --}}
    <script src="{{ asset('template/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    {{-- /sweetalert2 --}}
    {{-- datepicker --}}
    <script src="{{ asset('template/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    {{-- <script src="{{ asset('template/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script> --}}
    <script src="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/pickr/pickr.js') }}"></script>
    {{-- /datepicker --}}
    @yield('jsvendor')

    <!-- Main JS -->
    <script src="{{ asset('template/assets/js/main.js') }}"></script>
   <!-- Loading Page -->
   {{-- <script src="{{ url('/secure-js/js/app-loading-screen.js') }}"></script> --}}
   <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js//app-loading-screen.js']) }}"></script>

    <!-- Page JS -->
    {{-- <script src="template/assets/js/dashboards-analytics.js"></script> --}}
    @yield('jscustom')
  </body>
</html>
