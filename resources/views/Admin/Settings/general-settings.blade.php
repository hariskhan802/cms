

@extends('Admin.Layout.layout')

@section('content')

	<div class="main-wrap {{ $name.'-wrap' }}">
            

            
            
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
                        <form method="post" action="{{ route('general-settings') }}" novalidate enctype="multipart/form-data">
                            <div class="form-wrap">
                                <div class="form-head">
                                    <h6>Information</h6>
                                </div>
                                <div class="form-group">
                                    <label>Site Title</label>
                                    <input type="text" class="form-control" placeholder="Site Title" name="site_title" value="{{ get_option('site_title') }}" required>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label>Tagline</label>
                                    <input type="text" class="form-control" placeholder="Tagline" name="tagline" value="{{ get_option('tagline') }}"  required>
                                    <small class="error-msg"></small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Site Address (URL)</label>
                                    <input type="text" class="form-control" placeholder="Site Address (URL)" name="site_address_url" value="{{ get_option('site_address_url') }}"  required>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label>Administration Email Address</label>
                                    <input type="email" class="form-control" placeholder="Administration Email Address" name="administration_email_address" value="{{ get_option('administration_email_address') }}"  required>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group checkbox-fg">
                                    <label class="m-label">Membership</label>
                                    <input type="checkbox" class="form-control"  name="membership"  id="membership" {{ get_option('membership') == 'on' ? 'checked' : '' }} >
                                    <label for="membership" class="c-label">Anyone can register</label>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label class="m-label">New User Default Role</label>
                                    <select name="new_user_default_role" class="form-control">
                                        <option value="">Select Role</option>
                                        @if($roles->count() > 0)
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ get_option('new_user_default_role') == $role->id ? 'selected' : '' }}>{{ $role->role }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label>Admin Panel Date Format</label>
                                    <div class="radio-m-wrap">
                                        <input type="hidden" name="is_admin_panel_date_custom" class="cus-d-t-hidden" value="false" >
                                        <div class="radio-wrap">
                                            @foreach(get_date_formats() as $key => $format)
                                            
                                            <div class="radio-btn-wrap form-check">
                                                <input type="radio" class="form-check-input" id="{{ 'a-d-f-'.$key}}" name="admin_panel_date_format" value="{{ $format }}"  {{ (get_option('is_admin_panel_date_custom') == 'false' && get_option('admin_panel_date_format') == $format) ? 'checked' : '' }} >
                                                <label class="form-check-label" for="{{ 'a-d-f-'.$key}}"> 
                                                    <span class="d-f-p">{{ date($format) }}</span>
                                                    <code class="t-d-f">{{ $format }}</code>
                                                </label>
                                            </div>
                                            @endforeach
                                            
                                            <div class="radio-btn-wrap form-check ">
                                                <input type="radio" class="form-check-input cus-date-radio-btn " id="a-d-f-c"  name="admin_panel_date_format"  value="{{ get_option('admin_panel_custom_date_format')  }}"  {{ (get_option('is_admin_panel_date_custom')) == 'true' ? 'checked' : '' }}  c-g="is_admin_panel_date_custom"  >
                                                <label class="form-check-label" for="a-d-f-c"> 
                                                    <span class="d-f-p">Custom: </span>
                                                    <span>
                                                        <input type="text" class="cus-date-format" name="admin_panel_custom_date_format" value="{{ get_option('admin_panel_custom_date_format')  }}">
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label>Admin Panel Time Format</label>
                                    <div class="radio-m-wrap">
                                        <input type="hidden" name="is_admin_panel_time_custom"  class="cus-d-t-hidden"  value="false"  >
                                        <div class="radio-wrap">
                                            @foreach(get_time_formats() as $key => $format)
                                            
                                            <div class="radio-btn-wrap form-check">
                                                <input type="radio" class="form-check-input" id="{{ 'a-t-f-'.$key}}" name="admin_panel_time_format" value="{{ $format }}"  {{ (get_option('is_admin_panel_time_custom') == 'false' && get_option('admin_panel_time_format')  == $format) ? 'checked' : '' }}  >
                                                <label class="form-check-label" for="{{ 'a-t-f-'.$key}}"> 
                                                    <span class="d-f-p">{{ date($format) }}</span>
                                                    <code class="t-d-f">{{ $format }}</code>
                                                </label>
                                            </div>
                                            @endforeach
                                            <div class="radio-btn-wrap form-check ">
                                                <input type="radio" class="form-check-input cus-date-radio-btn " id="a-t-f-c" name="admin_panel_time_format" value="{{ get_option('admin_panel_custom_time_format') }}" {{ (get_option('is_admin_panel_time_custom') == 'true' ) ? 'checked' : '' }}  c-g="is_admin_panel_time_custom"  >
                                                <label class="form-check-label" for="a-t-f-c"> 
                                                    <span class="d-f-p">Custom: </span>
                                                    <span>
                                                        <input type="text" class="cus-date-format" name="admin_panel_custom_time_format" value="{{ get_option('admin_panel_custom_time_format') }}" >
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label>Website Date Format</label>
                                    <div class="radio-m-wrap">
                                        <input type="hidden" name="is_website_date_custom"  class="cus-d-t-hidden"  value="false"  >
                                        <div class="radio-wrap">
                                            @foreach(get_date_formats() as $key => $format)
                                            <div class="radio-btn-wrap form-check">
                                                <input type="radio" class="form-check-input" id="{{ 'w-d-f-'.$key}}" name="website_date_format" value="{{ $format }}"  {{ (get_option('is_website_date_custom') == 'false' && get_option('website_date_format') == $format) ? 'checked' : '' }}  >
                                                <label class="form-check-label" for="{{ 'w-d-f-'.$key}}"> 
                                                    <span class="d-f-p">{{ date($format) }}</span>
                                                    <code class="t-d-f">{{ $format }}</code>
                                                </label>
                                            </div>
                                            @endforeach
                                            <div class="radio-btn-wrap form-check ">
                                                <input type="radio" class="form-check-input cus-date-radio-btn " id="w-d-f-c" name="website_date_format" value="{{ get_option('website_custom_date_format') }}"  {{ (get_option('is_website_date_custom') == 'true' ) ? 'checked' : '' }}  c-g="is_website_date_custom" >
                                                <label class="form-check-label" for="w-d-f-c"> 
                                                    <span class="d-f-p">Custom: </span>
                                                    <span>
                                                        <input type="text" name="website_custom_date_format" class="cus-date-format" value="{{ get_option('website_custom_date_format') }}">
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label>Website Time Format</label>
                                    <div class="radio-m-wrap">
                                        <input type="hidden" name="is_website_time_custom"  class="cus-d-t-hidden"  value="false"  >
                                        <div class="radio-wrap">
                                            @foreach(get_time_formats() as $key => $format)
                                            <div class="radio-btn-wrap form-check">
                                                <input type="radio" class="form-check-input" id="{{ 'w-t-f-'.$key}}" name="website_time_format" value="{{ $format }}"  {{ (get_option('is_website_time_custom') == 'false' && get_option('website_time_format') == $format) ? 'checked' : '' }}  >
                                                <label class="form-check-label" for="{{ 'w-t-f-'.$key}}"> 
                                                    <span class="d-f-p">{{ date($format) }}</span>
                                                    <code class="t-d-f">{{ $format }}</code>
                                                </label>
                                            </div>
                                            @endforeach
                                            <div class="radio-btn-wrap form-check ">
                                                <input type="radio" class="form-check-input  cus-date-radio-btn  " id="w-t-f-c" name="website_time_format" value="{{ get_option('website_custom_time_format') }}"  {{ (get_option('is_website_time_custom') == 'true') ? 'checked' : '' }}  c-g="is_website_time_custom"  >
                                                <label class="form-check-label" for="w-t-f-c"> 
                                                    <span class="d-f-p">Custom: </span>
                                                    <span>
                                                        <input type="text" name="website_custom_time_format" class="cus-date-format" value="{{ get_option('website_custom_time_format') }}">
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <small class="error-msg"></small>
                                </div>
                                <div class="form-group">
                                    @csrf
                                    <input type="submit" name="submit" value="Update" class="btn btn-primary pull-right">
                                </div>
                            </div>
                        </form>                        
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