<!DOCTYPE html>
<html lang="en">

<head>
    @include('Admin.Layout.top-scripts')
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-6 col-md-6">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        @yield('content')
                    </div>
                </div>

            </div>

        </div>

    </div>
    @include('Admin.Layout.bottom-scripts')
</body>
</html>