<!DOCTYPE html>
<html lang="en">

<head>
@include('Admin.Layout.top-scripts')
@php
    $currentPostType = (isset($currentPostType) && is_array($currentPostType)) ? $currentPostType : '';
    $currentTaxonomy = (isset($currentTaxonomy) && is_array($currentTaxonomy)) ? $currentTaxonomy : '';
@endphp
<script type="text/javascript">
    localStorage.setItem('app_url', '{{ url('') }}');
    localStorage.setItem('image_extensions', '{{ get_image_extensions("string") }}');
    var postType = '{{ array_value($currentPostType, "post_type") }}';
    var taxonomy = '{{ array_value($currentTaxonomy, "taxonomy") }}';
</script>
{!! do_action('wp_head') !!}
</head>
@php

    $id = \Request::route('id') ? \Request::route('id') : '0';
    $postType = '';
    $taxonomy = '';
    if($currentPostType != '') {
        $name = array_value($currentPostType, 'post_type') != '' ? 'post' : $name ;
        $postType = 'post-type';
    }
    if($currentTaxonomy != '') {
        $name = 'term';
        $taxonomy = 'taxonomy';
        $postType = '';
    }
    $atts = [
        'page-name' => $name,
        'edit-page-id' => $id,
        'parent-page-name' => word_format($name, 'plural')
    ];
    if (Route::is('edit-*')  ) {
        $atts['edit-page-url'] = route(\Request::route()->getName(), \Request::route()->parameter('id'));
        $atts['edit-page-url'] .= array_value($currentTaxonomy, 'taxonomy') != '' ? '?taxonomy='.array_value($currentTaxonomy, 'taxonomy').'&post_type='.array_value($currentPostType, 'post_type') : '';
    }
@endphp
<body id="page-top" class="{{ get_admin_body_classes(str_replace(' ','-', $name).'-m-wrap '.$postType. ' '.$taxonomy) }}" {{ get_admin_body_attributes($atts) }}>

    @php
        $name = array_value($currentTaxonomy, 'taxonomy');
    @endphp
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