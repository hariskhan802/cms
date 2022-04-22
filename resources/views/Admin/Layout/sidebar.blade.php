		<!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Ziltex CMS</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }} " href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            @php
                // print_r(get_post_types()); die;
            @endphp
            <!-- Divider -->
            <hr class="sidebar-divider">
            @if(is_array(get_post_types_object()))
                @php
                    // print_r(get_post_types_object());die;
                @endphp
                @foreach(get_post_types_object() as $key => $postType)
                <li class="nav-item {{ ( (Route::is('posts') || Route::is('add-post')  || Route::is('edit-post')) && \Request::get('post_type') == array_value(get_post_types(), $key) ) ? 'active' : '' }}">
                    <a class="nav-link " href="{{ route('posts').'?post_type='.array_value(get_post_types(), $key) }}" >
                        <i class="fas fa-fw fa-folder"></i>
                        <span>{{ array_value($postType, 'name')  }}</span>
                    </a>
                    <div id="collapsePosts" class="cus-sub-menu collapse  {{ ( (Route::is('posts') || Route::is('add-post')  || Route::is('edit-post')) && \Request::get('post_type') == array_value(get_post_types(), $key) ) ? 'show' : '' }} " aria-labelledby="headingPages"
                        data-parent="#accordionSidebar">
                        <ul>
                            <li class="{{ (Route::is('posts') && \Request::get('post_type') == array_value(get_post_types(), $key) ) ? 'active' : '' }}">
                                <a href="{{ get_admin_post_type_url(['name' => 'posts'], array_value(get_post_types(), $key)) }}">View</a>
                            </li>
                            <li class="{{ (Route::is('add-post') && \Request::get('post_type') == array_value(get_post_types(), $key) ) ? 'active' : '' }}">
                                <a href="{{ get_admin_post_type_url(['name' => 'add-post'], array_value(get_post_types(), $key)) }}">Add New</a>
                            </li>
                            @if (is_array(array_value($postType, 'taxonomies')))
                                                                
                                @foreach(array_value($postType, 'taxonomies') as $taxKey => $taxonomy)
                                <li class="{{ (Route::is('add-post') && \Request::get('post_type') == array_value(get_post_types(), $key) ) ? 'active' : '' }}">
                                    <a href="{{ get_admin_post_type_url(['name' => 'add-post'], array_value(get_post_types(), $key)) }}">{{-- $taxonomy['name'] --}}</a>
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </li>
                @endforeach
            @endif
            <!-- <li class="nav-item {{ (Route::is('posts') || Route::is('categories')) ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('posts') }}" >
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Posts</span>
                </a>
                <div id="collapsePosts" class="collapse {{ (Route::is('posts') || Route::is('categories')) ? 'show' : '' }}" aria-labelledby="headingPages"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ Route::is('categories') ? 'active' : '' }}" href="{{ route('categories') }}">Categories</a>
                        
                    </div>
                </div>
            </li> 
            <li class="nav-item {{ Route::is('pages') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pages') }}">
                    <i class="fas fa-fw fa-copy"></i>
                    <span>Pages</span></a>
            </li> -->

            <li class="nav-item {{ Route::is('templates') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('templates') }}">
                    <i class="fas fa-fw fa-copy"></i>
                    <span>Templates</span></a>
            </li>

            <li class="nav-item {{ Route::is('comments') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('comments') }}">
                    <i class="fas fa-fw fa-comment"></i>
                    <span>Comments</span></a>
            </li>
            <li class="nav-item {{ Route::is('users') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Users</span></a>
            </li>
            <li class="nav-item {{ Route::is('roles') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('roles') }}">
                    <i class="fas fa-fw fa-copy"></i>
                    <span>Roles</span></a>
            </li>
            <li class="nav-item {{ Route::is('profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('profile') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Profile</span></a>
            </li>
            

            

            <li class="nav-item {{ Route::is('general-settings') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('general-settings') }}" >
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Settings</span>
                </a>
                <div id="collapsePosts" class="collapse {{ (Route::is('general-settings') || Route::is('roles-settings')) ? 'show' : '' }}" aria-labelledby="headingPages"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ Route::is('general-settings') ? 'active' : '' }}" href="{{ route('general-settings') }}">General Settings</a>
                        <a class="collapse-item {{ Route::is('roles-settings') ? 'active' : '' }}" href="{{ route('roles-settings') }}">Roles Settings</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->