<!DOCTYPE html>
<html lang="en">

<head>
@include('Admin.Layout.top-scripts')
@php
    $postType = (isset($postType) && !empty($postType)) ? $postType : '';
@endphp
<script type="text/javascript">
    localStorage.setItem('app_url', '{{ url('') }}');
    localStorage.setItem('image_extensions', '{{ get_image_extensions("string") }}');
    var postType = '{{ $postType }}';
</script>
{!! do_action('wp_head') !!}
</head>
@php

    $id = \Request::route('id') ? \Request::route('id') : '0';
    
    if($postType != '') {
        $name = $postType;
        $postType = 'post-type';
    }
    $atts = [
        'page-name' => $name,
        'edit-page-id' => $id,
        'parent-page-name' => word_format($name, 'plural')
    ];
    if (Route::is('edit-*')  ) {
        $atts['edit-page-url'] = route(\Request::route()->getName(), \Request::route()->parameter('id'));
    }
@endphp
<body id="page-top" class="{{ get_admin_body_classes(str_replace(' ','-', $name).'-m-wrap '.$postType) }}" {{ get_admin_body_attributes($atts) }}>

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