 <!-- Core JS -->
 <!-- build:js assets/vendor/js/core.js -->
 <script src="{{ asset('assets-vendor') }}/vendor/libs/jquery/jquery.js"></script>
 <script src="{{ asset('assets-vendor') }}/vendor/libs/popper/popper.js"></script>
 <script src="{{ asset('assets-vendor') }}/vendor/js/bootstrap.js"></script>
 <script src="{{ asset('assets-vendor') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

 <script src="{{ asset('assets-vendor') }}/vendor/js/menu.js"></script>
 <!-- endbuild -->

 <!-- Vendors JS -->
 <script src="{{ asset('assets-vendor') }}/vendor/libs/apex-charts/apexcharts.js"></script>

 <!-- Main JS -->
 <script src="{{ asset('assets-vendor') }}/js/main.js"></script>

 <!-- Page JS -->
 <script src="{{ asset('assets-vendor') }}/js/dashboards-analytics.js"></script>

 <!-- Place this tag in your head or just before your close body tag. -->
 <script async defer src="https://buttons.github.io/buttons.js"></script>

 <script>
     $(document).ready(function() {
         $(document).on('click', '.notificationsIcon', function() {
             $.ajax({
                 url: {{ Illuminate\Support\Js::from(route('vendor.notifications.read')) }},
                 method: 'get',
                 success: function(response) {
                     $('.notificationsIcon').load('.notificationsIcon >*');
                     $('.notificationModal').load('.notificationModal >*');
                 },
                 error: function(response) {
                     alert('try again ..');
                 },
             });
         });
     });
 </script>
 <script>
     $(document).ready(function() {
         $(document).on('click', '.notificationClear', function() {
             $.ajax({
                 url: {{ Illuminate\Support\Js::from(route('vendor.notifications.clear')) }},
                 method: 'get',
                 success: function(response) {
                     $('.notificationsIcon').load('.notificationsIcon >*');
                     $('.notificationModal').load('.notificationModal >*');
                 },
                 error: function(response) {
                     alert('try again ..');
                 },
             });
         });
     });
 </script>
