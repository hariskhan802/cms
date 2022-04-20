

@extends('Admin.Layout.layout')

@section('content')

	<div class="main-wrap {{ $name.'-wrap' }}">
            
            <div class="main-c-wrap roles-settings-wrap">

            @if(session('errormsg'))
            <div class="card mb-4 border-left-danger">
                <div class="card-body">
                    {{ session('errormsg') }}
                </div>
            </div>
            @endif
            <form method="post" action="{{ route('roles-settings') }}" enctype="multipart/form-data">
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ word_format($name, 'ucfirst')  }}</h6>
                </div>
                
                <div class="card-body">
                    <div class="tabs-wrap">
                        <div class="tabs-menu">
                            <ul>
                                @foreach($roles as $key1 => $role)
                                <li class="{{ $key1 == 0 ? 'active' : '' }}">
                                    <a href="#{{ strtolower($role->role) }}" class="tab">{{$role->role}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tabs-div">

                            @foreach($roles as $key1 => $role)
                            <div class="tab-div" id="{{ strtolower($role->role) }}">
                                <div class="form-wrap">
                                    <div class="form-head">
                                        <h6>{{$role->role}}</h6>
                                        <input type="checkbox" class="checked-roles">
                                    </div>
                                    @foreach(get_roles_permissions() as $key2 => $role_p)
                                    @php 
                                    $permissions = json_decode($role->permissions) ? json_decode($role->permissions) : [];
                                    @endphp
                                    <div class="form-group-wrap">
                                        <div class="form-c-wrap">
                                            <label class="m-label">{{ ucfirst($key2) }}</label>
                                            <input type="checkbox" class="checked-roles">
                                            @foreach($role_p as $key3 => $r)
                                            <?php // var_dump($r) ?>
                                            
                                            <div class="form-group checkbox-fg">
                                                <input type="checkbox" class="form-control"  name="{{ 'roles['.strtolower($role->role).']['.$r.']' }}" id="{{ 'roles['.strtolower($role->role).']['.$r.']' }}"  {{ (in_array($r, $permissions) ? 'checked=checked' : '') }}>
                                                <label for="{{ 'roles['.strtolower($role->role).']['.$r.']' }}" class="c-label">{{ ucwords(str_replace('_', ' ', $r)) }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="form-group">
                                        <input type="hidden" name="_token" value="{{ @csrf_token() }}">
                                        <input type="submit" name="submit" value="Update" class="btn btn-primary pull-right">
                                        
                                    </div>
                                </div>
                                
                            </div>
                            @endforeach
                            <div class="tab-div" id="subscriber">
                                
                            </div>
                        </div>
                        <div class="card mb-4 c-msg border-left-success">
                            <div class="card-body"></div>
                        </div>
                    </div>

                    
                    
                </div>
            </div>
            </form>          
            </div>
        </form>
    </div>

    
@endsection