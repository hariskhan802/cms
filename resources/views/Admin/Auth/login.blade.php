    
    @extends('Admin.Layout.layout-2')

    @section('content')
    <!-- Nested Row within Card Body -->
    <div class="row">
        <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
        <div class="col-lg-12">
            <div class="p-5">
                <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                </div>
                <form class="user" method="post">
                    <div class="form-group">
                        <input type="email" class="form-control form-control-user"
                            id="exampleInputEmail" aria-describedby="emailHelp"
                            placeholder="Email Address" name="email"  required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-user"
                            id="exampleInputPassword" placeholder="Password" name="password" required>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox small">
                            <input type="checkbox" class="custom-control-input" name="rememberme" id="rememberme">
                            <label class="custom-control-label" for="rememberme">Remember
                                Me</label>
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="submit" name="submit" value="Login" class="btn btn-primary btn-user btn-block" />

                    @if(session('errormsg'))
                    <div class="card mb-4 border-left-danger">
                        <div class="card-body">
                        {{ session('errormsg') }}
                        </div>
                    </div>
                    @endif
                    <hr>
                    <!-- <a href="index.html" class="btn btn-google btn-user btn-block">
                        <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-user btn-block">
                        <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a> -->
                </form>
                <!-- <hr>
                <div class="text-center">
                    <a class="small" href="forgot-password.html">Forgot Password?</a>
                </div>
                <div class="text-center">
                    <a class="small" href="register.html">Create an Account!</a>
                </div> -->
            </div>
        </div>
    </div>
    @endsection