<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    @include('includes.head')
    @yield('title')
</head>
<body>
    @include('includes.navbar')

    <div class="container-fluid px-5 py-5">
        @yield('content')
    </div>

    @include('includes.footer')

    <script src="{{ asset('theme/js/jquery-3.5.1.slim.min.js') }}"></script>
    <script src="{{ asset('theme/js/popper.min.js') }}"></script>
    <script src="{{ asset('theme/js/bootstrap.bundle.min.js') }}"></script>
    
    <script src="{{ asset('theme/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable()
            $('[data-toggle="tooltip"]').tooltip()
        } );

        setTimeout(function(){
            $('#alert-msg').hide()
        }, 3000);
    </script>

    @yield('page-script')
</body>
</html>