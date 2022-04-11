<!DOCTYPE html>
<html lang="en">

<head>
@include('Admin.Layout.top-scripts')
<script type="text/javascript">
    localStorage.setItem('app_url', '{{ url('') }}');
    localStorage.setItem('image_extensions', '{{ __get_image_extensions("string") }}');

</script>
{!! do_action('wp_head') !!}
</head>
@php
    $id = \Request::route('id') ? \Request::route('id') : '0';
    $atts = [
        'page-name' => $name,
        'edit-page-id' => $id,
        'parent-page-name' => __word_format($name, 'plural')
    ];
    
@endphp
<body id="page-top" class="{{ __get_admin_body_classes(str_replace(' ','-', $name).'-m-wrap ') }}" {{ __get_admin_body_attributes($atts) }}>

    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('Admin.Layout.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('Admin.Layout.topbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                    

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            
            @include('Admin.Layout.footer')

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    

    
    @include('Admin.Layout.bottom-scripts')
</body>

</html>