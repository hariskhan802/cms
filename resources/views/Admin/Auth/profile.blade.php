

@extends('Admin.Layout.layout')

@section('content')

	<div class="main-wrap  {{ $name.'-wrap' }}">
            

            
            
            <div class="main-c-wrap profile-wrap">

            
            @if(session('errormsg'))
            <div class="card mb-4 border-left-danger">
                <div class="card-body">
                    {{ session('errormsg') }}
                </div>
            </div>
            @endif
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ word_format($name, 'ucfirst')  }}</h6>
                </div>
                
                <div class="card-body">
                    <div class="tabs-wrap">
                        <div class="tabs-menu">
                            <ul>
                                <li class="active">
                                    <a href="#information" class="tab">Information</a>
                                </li>
                                <li>
                                    <a href="#change-password"  class="tab">Change Password</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tabs-div">
                            <div class="tab-div" id="information">
                                <form method="post" action="{{ route('profile') }}" enctype="multipart/form-data">
                                    <div class="form-wrap">
                                        <div class="form-head">
                                            <h6>Information</h6>
                                        </div>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" placeholder="Name" name="name" value="{{ array_value(c_user(), 'display_name') }}" required>
                                            <small class="error-msg"></small>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" placeholder="Email" name="email" value="{{ array_value(c_user(), 'user_email') }}"  required>
                                            <small class="error-msg"></small>
                                        </div>
                                        
                                        <div class="form-group img-f-g">
                                            <label>Image</label>
                                            <input type="file" name="image" accept="image/*" {{ array_value(c_user(), 'image') == '' ? 'required' : '' }} />
                                            <small class="error-msg"></small>
                                            
                                            <img src="{{ get_user_image(array_value(c_user(), 'image')) }}" width="50">
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="_image" value="{{array_value(c_user(), 'image')}}">
                                            <input type="hidden" name="_token" value="{{ @csrf_token() }}">
                                            <input type="submit" name="submit" value="Update" class="btn btn-primary pull-right">
                                            
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-div" id="change-password">
                                <form method="post" action="{{ route('profile') }}">
                                    <div class="form-wrap">
                                        <div class="form-head">
                                            <h6>Change Password</h6>
                                        </div>
                                        <div class="form-group">
                                            <label>Current Password</label>
                                            <input type="password" class="form-control" placeholder="Current Password" name="current_password" required>
                                            <small class="error-msg"></small>
                                            <a href="javascript:;" class="p-visibility"><i class="fa fa-eye"></i></a>
                                        </div>
                                        <div class="form-group">
                                            <label>New Password</label>
                                            <input type="password" class="form-control" placeholder="New Password" name="new_password" required>
                                            <small class="error-msg"></small>
                                            <a href="javascript:;" class="p-visibility"><i class="fa fa-eye"></i></a>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Confirm Password</label>
                                            <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" required>
                                            <small class="error-msg"></small>
                                            <a href="javascript:;" class="p-visibility"><i class="fa fa-eye"></i></a>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="hidden" name="_token" value="{{ @csrf_token() }}">
                                            <input type="submit" name="submit" value="Change Password" class="btn btn-primary">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card mb-4 c-msg border-left-success">
                            <div class="card-body"></div>
                        </div>
                    </div>

                    
                    
                </div>
            </div>
                            
            </div>
        </form>
    </div>

    
@endsection