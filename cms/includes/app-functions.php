<?php
    use App\Models\Option;
    use App\Models\Category;
    use App\Models\PostCategoryRelation;
    use App\Models\Role;
    function __data_table($data) {
        // $inputs, $table, $columns, $tColumns
        $columnsF = [];
        foreach ($data['columns'] as $key => $column) {
            $columnsF[] = ['data' => stripos($column, 'as') != false ? str_replace(' ', '', substr($column, stripos($column, 'as')+2)) : $column];
        }
        // print_r($columnsF); die;
        $response['frontend'] = "<div class='card shadow mb-4'>
        <div class='card-header py-3'>
            <h6 class='m-0 font-weight-bold text-primary'>".@$data['title']."</h6>
        </div>
        <div class='card-body'><table class='c-datatable table table-bordered' data-columns='".json_encode($columnsF)."' data-url='".route('pages-ajax')."'  >
        <thead>
            <tr>";
        foreach ($data['tColumns'] as $key => $tColumn) {
            if(__is_image($tColumn)) {
                $response['frontend'] .= '<td class="t1"><img src="'.$tColumn.'" width="50" /></td>';
            }
            else {
                $response['frontend'] .= '<td class="t2">'.$tColumn.'</td>';
            }
            
        }
        $response['frontend'] .= ' </tr>
            </thead>
        </table></div>
        </div>';
        if(isset($data['inputs'])){
            $inputs = $data['inputs'];
            
            $columnIndex = $inputs['order'][0]['column'];
            $columnName = $inputs['columns'][$inputs['order'][0]['column']]['data'];
            $columnSortOrder = $inputs['order'][0]['dir'];
            $searchValue = $inputs['search']['value'];

            $totalRecords = $data['table']::select('count(id) as allcount')->count();
            $totalRecordswithFilter = $data['table']::select('count(id) as allcount')->where('title', 'like', '%' .$searchValue . '%')->count();

            $records = $data['table']::orderBy($columnName,$columnSortOrder)
            ->where('pages.title', 'like', '%' .$searchValue . '%')
            ->select($data['columns'])
            ->skip($inputs['start'])
            ->take($inputs['length'])
            ->get()
            ->toArray();
            
        
                
            
            // $columnsF;
            $response['backend'] = array(
                "draw" => intval($inputs['draw']),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $records
            );
        }
        return $response;
    }
    function __is_image($image) {
        $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];

        $explodeImage = explode('.', $image);
        $extension = end($explodeImage);
        // var_dump($extension);
        // var_dump(in_array($extension, $imageExtensions)); 
        return in_array($extension, $imageExtensions);
    }

    function __word_format($name, $type = null) {

        if (!$type) {
           $name = $name;
        }
        else if ($type == 'ucfirst') {
           $name = ucfirst($name);
        }
        else if ($type == 'ucwords') {
           $name = ucwords($name);
        }
        else if ($type == 'plural') {
           $name = \Illuminate\Support\Str::plural($name);
        }
        else if ($type == 'cPlural') {
           $name =  ucfirst(\Illuminate\Support\Str::plural($name));
        }
        return $name;
    }

    /*

    function __get_parent_child_categories_dropdown($id) {
        global $html, $dash;
        if ($id != '') {
            $category1 = Category::query();
            if (\__c_user()->user_type != 'admin') {
                $category1->where(['user_id' => \__c_user()->id]);
            }
            $category1 = $category1->where(['parent_id' => $id])->get();
            
            if ($category1->count() > 0) {
                $dash .= '-';
                foreach($category1 as $cat) {
                    $html .= '<option value="'.$cat->id.'"> '.$dash.' '.$cat->title.'</option>';
                    __get_parent_child_categories_dropdown($cat->id);
                }
            }
        }

        return $html;                           
    }

    function __get_parent_child_categories_table($id = 0) {
        global $html2;
        global $dash2;
        if ($id != '') {
            $name = 'category';
            $category1 = Category::query();
            if (\__c_user()->user_type != 'admin') {
                $category1->where(['user_id' => \__c_user()->id]);
            }
            $category1 = $category1->where(['parent_id' => $id])->get();
            
            if ($category1->count() > 0) {
                $dash2 .= '-';
                
                foreach($category1 as $cat) {
                    
                    $html2 .= '<tr>
                                <td><input type="checkbox" name="action_ids[]" value="'.$cat->id.'"></td>
                                <td>'.$cat->id.'</td>
                                <td> '.$dash2.' '.$cat->title.'</td>
                                <td>'.$cat->description.'</td>
                                <td><img src="'.__get_image($cat->featured_image).'" width="50"></td>
                                <td>'.$cat->created_at->diffForHumans().'</td>
                                <td class="action">';
                                $html2 .= '<a href="'.route('edit-'.__word_format($name), $cat->id).'" class="edit-record">
                                    <i class="fa fa-pencil-alt"></i>
                                    </a>';
                                $html2 .= '<a href="'.route('delete-'.__word_format($name), $cat->id).'" 
                                class="danger-delete" >
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr> ';
                    
                    __get_parent_child_categories_table($cat->id);
                }
            }

        }

        return $html2;                           
    }
    */
    function __category_html($cat, $dash2) {
        $name = 'category';
        return '<tr>
                                <td><input type="checkbox" name="action_ids[]" value="'.$cat->id.'"></td>
                                <td>'.$cat->id.'</td>
                                <td> '.$dash2.' '.$cat->title.'</td>
                                <td>'.$cat->description.'</td>
                                <td><img src="'.__get_image($cat->featured_image).'" width="50"></td>
                                <td>'.$cat->created_at->diffForHumans().'</td>
                                <td class="action">
                                    <a href="'.route('edit-'.__word_format($name), $cat->id).'" class="edit-record">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <a href="'.route('delete-'.__word_format($name), $cat->id).'" class="danger-delete" >
                                            <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr> ';
    }

    function __get_parent_child_categories_dropdown($id) {
        $html = '';
        $dash = '';
        if ($id != '') {
            $category1 = Category::query();
            if (\__c_user()->user_type != 'admin') {
                $category1->where(['user_id' => \__c_user()->id]);
            }
            $category1 = $category1->where(['parent_id' => $id])->get();
            if ($category1->count() > 0) {
                $dash = '-';
                foreach($category1 as $cat) {
                    $html .= '<option value="'.$cat->id.'"> '.$dash.' '.$cat->title.'</option>';
                    $category2 = Category::query();
                    if (\__c_user()->user_type != 'admin') {
                        $category2->where(['user_id' => \__c_user()->id]);
                    }
                    $category2 = $category2->where(['parent_id' => $cat->id])->get();
                    if ($category2->count() > 0) {
                        $dash = '--';
                        foreach($category2 as $cat2) {
                            $html .= '<option value="'.$cat2->id.'"> '.$dash.' '.$cat2->title.'</option>';
                            $category3 = Category::query();
                            if (\__c_user()->user_type != 'admin') {
                                $category3->where(['user_id' => \__c_user()->id]);
                            }
                            $category3 = $category3->where(['parent_id' => $cat2->id])->get();
                            if ($category3->count() > 0) {
                                $dash = '---';
                                foreach($category3 as $cat3) {
                                    $html .= '<option value="'.$cat3->id.'"> '.$dash.' '.$cat3->title.'</option>';
                                    $category4 = Category::query();
                                    if (\__c_user()->user_type != 'admin') {
                                        $category4->where(['user_id' => \__c_user()->id]);
                                    }
                                    $category4 = $category4->where(['parent_id' => $cat3->id])->get();
                                    if ($category4->count() > 0) {
                                        $dash = '----';
                                        foreach($category4 as $cat4) {
                                            $html .= '<option value="'.$cat4->id.'"> '.$dash.' '.$cat4->title.'</option>';
                                            $category5 = Category::query();
                                            if (\__c_user()->user_type != 'admin') {
                                                $category5->where(['user_id' => \__c_user()->id]);
                                            }
                                            $category5 = $category5->where(['parent_id' => $cat4->id])->get();
                                            if ($category5->count() > 0) {
                                                $dash = '-----';
                                                foreach($category5 as $cat5) {
                                                    $html .= '<option value="'.$cat5->id.'"> '.$dash.' '.$cat5->title.'</option>';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $html;                           
    }

    function __get_parent_child_categories_table($id = 0) {
        // global $html2;
        // global $dash2;
        $html2 = '';
        $dash2 = '';
        if ($id != '') {
            $name = 'category';
            $category1 = Category::query();
            if (\__c_user()->user_type != 'admin') {
                $category1->where(['user_id' => \__c_user()->id]);
            }
            $category1 = $category1->where(['parent_id' => $id])->get();
            
            if ($category1->count() > 0) {
                $dash2 = '-';
                
                foreach($category1 as $cat) {
                    
                    $html2 .= __category_html($cat, $dash2);
                    $category2 = Category::query();
                    if (\__c_user()->user_type != 'admin') {
                        $category2->where(['user_id' => \__c_user()->id]);
                    }
                    $category2 = $category2->where(['parent_id' => $cat->id])->get();
                        
                    if ($category2->count() > 0) {
                        $dash2 = '--';
                        
                        foreach($category2 as $cat2) {
                            
                            $html2 .= __category_html($cat2, $dash2);

                            $category3 = Category::query();
                            if (\__c_user()->user_type != 'admin') {
                                $category3->where(['user_id' => \__c_user()->id]);
                            }
                            $category3 = $category3->where(['parent_id' => $cat2->id])->get();
                            // var_dump($category3->count()); die;
                            if ($category3->count() > 0) {
                                $dash2 = '---';
                                // dd($category3->toArray());
                                foreach($category3 as $cat3) {
                                    
                                    $html2 .= __category_html($cat3, $dash2);

                                    $category4 = Category::query();
                                    if (\__c_user()->user_type != 'admin') {
                                        $category4->where(['user_id' => \__c_user()->id]);
                                    }
                                    $category4 = $category4->where(['parent_id' => $cat3->id])->get();
                                    // var_dump($category4->count()); die;
                                    if ($category4->count() > 0) {
                                        $dash2 .= '-';
                                        
                                        foreach($category4 as $cat4) {
                                            $html2 .= __category_html($cat4, $dash2);
                                            $category5 = Category::query();
                                            if (\__c_user()->user_type != 'admin') {
                                                $category5->where(['user_id' => \__c_user()->id]);
                                            }
                                            $category5 = $category5->where(['parent_id' => $cat4->id])->get();
                                            // var_dump($category5->count()); die;
                                            if ($category5->count() > 0) {
                                                $dash2 .= '-';
                                                
                                                foreach($category5 as $cat5) {
                                                    $html2 .= __category_html($cat5, $dash2);
                                                }
                                            }
                                            
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        return $html2;                           
    }

    function __get_category_string_format($id) {
        return array_column(Category::select(['title'])
        ->whereIn(
            'id', PostCategoryRelation::select(['cat_id'])->where(['post_id' => $id])->get()->pluck('cat_id')
        )->get()->toArray(), 'title');
    }

    function __get_option($option_name) {
        $option_value =  Option::select(['option_value'])->where(['option_name' => $option_name])->pluck('option_value')->first();
        return  $option_value == '[]' ? '' : $option_value;
    }
    function __add_option($option_name, $option_value) {
        $success = false;
        if(Option::where('option_name', $option_name)->count() > 0 ) {
            $success = false;
        }
        else if(Option::create(['option_name' => $option_name, 'option_value' => $option_value])) {
            $success = true;
        }
        return $success;
    }

    function __update_option($option_name, $option_value) {
        $success = false;
        
        if(Option::where('option_name', $option_name)->count() > 0 ) {
            if (Option::where(['option_name' => $option_name])->update(['option_value' => $option_value])) {
                $success = true;
            }
            
        }
        else if(Option::create(['option_name' => $option_name, 'option_value' => $option_value])) {
            $success = true;
        }
        return $success;
    }
    
    function __get_user_image($img) {
        $imgPath = public_path('/assets/images/'.$img);
        $url = File::exists($imgPath) && File::isFile($imgPath) ? asset('public/assets/images/'.$img) : asset('public/assets/images/demo_profile.svg');
        return $url;
    }
    function __get_image($img) {
        $imgPath = public_path('/assets/images/'.$img);
        $url = File::exists($imgPath) && File::isFile($imgPath)  ? asset('public/assets/images/'.$img) : asset('public/assets/images/placeholder-img.jpg');
        return $url;
    }
    function __c_user() {
        return Auth::user();
    }

    function __get_roles_permissions() {
        return [
                'dashboard' => [
                    'view_dashboard',
                ],
                'posts' => [
                    'create_posts',
                    'delete_posts',
                    'edit_posts',
                    'list_posts',
                    'manage_own_posts',
                ],
                'pages' => [
                    'create_pages',
                    'delete_pages',
                    'edit_pages',
                    'list_pages',
                    'manage_own_pages',
                ],
                'comments' => [
                    'delete_comments',
                    'edit_comments',
                    'list_comments',
                    'manage_own_comments'
                ],
                'users' => [
                    'create_users',
                    'delete_users',
                    'edit_users',
                    'list_users',
                ],
                'settings' => [
                    'list_settings',
                    'edit_settings',
                ],
            ];
    }

    function __get_roles() {
        return Role::select(['id', 'role', 'permissions'])->get();
    }

    function __get_image_extensions($type = 'string') {
        $ext = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'webp'];
        if ($type == 'string') 
            $ext = implode(',', $ext);
        return $ext;
    }

    function __get_admin_body_classes($class = '') {
        $classes = ''; 
        if (Route::is('edit-*')  ) {
            $classes .= ' edit-page';
        }
        $classes .= ' '.$class;
        return $classes;
    }

    function __get_admin_body_attributes($attribute = []) {
        $attributes = ''; 
        
        foreach ($attribute as $key => $att) {
            $attributes .= ' '.$key.'='.$att.'';
        }
        
        return $attributes;
    }
    function get_admin_post_type_url($routeParams, $post_type) {
        $route = null;
        if (array_value($routeParams, 'name') != null && array_value($routeParams, 'id') != null) {
            $route = route(array_value($routeParams, 'name'), array_value($routeParams, 'id')).'?post_type='.$post_type;
        }
        elseif(array_value($routeParams, 'name') != null) {
            $route = route(array_value($routeParams, 'name')).'?post_type='.$post_type;
        }
        return $route;
    }

    function cms_error($error) {
        return '<div class="cms-error"><h6>'.$error.'</h6></div>';
    }
    if (!function_exists('array_value')) {

        function array_value($array, $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                return $array[$key];
            }
        }

    }
    function add_action($hook_name = '', $callback = '', $priority = 10) {
        global $cmsHooks;
        $registered_hooks = ['init', 'wp_head', 'wp_footer'];
        if (($hook_name == '' || $callback == '') || ($hook_name == '' && $callback == '') ) {
            echo cms_error('First and Second Arguments can not be empty!');
            // return false;
        }
        else if (is_string($callback) && !function_exists($callback)) {
            echo cms_error('Call back function is not exist!');
            // return false;
        }
        else {
            $cmsHooks[] = [
                'hook_name' => $hook_name,
                'callback' => $callback,
                'priority' => $priority,
            ];
        }
    }

    function do_action($hook_name ='') {
        global $cmsHooks;
        $actionRespoonse = '';
        if ($cmsHooks && count($cmsHooks) > 0) {
            $priority = array_column($cmsHooks, 'priority');
            array_multisort($priority, SORT_ASC, $cmsHooks);
            foreach ($cmsHooks as $key => $cmsHook) {
                if (isset($cmsHook['hook_name']) && !empty($cmsHook['hook_name'])) {
                    if ($cmsHook['hook_name'] == $hook_name) {
                        $callback = $cmsHook['callback'];
                        $actionRespoonse .= $callback();
                    }
                }
            }
            
        }
        return $actionRespoonse;
    }

    function register_post_type($args = []) {
        global $cmsPostTypes;
        $cmsPostTypes[] = $args;

    }

    function get_post_types_object() {
        global $cmsPostTypes;
        $menuPosition = array_column($cmsPostTypes, 'menu_position');
        array_multisort($menuPosition, SORT_ASC, $cmsPostTypes);
        return $cmsPostTypes;
    }

    function get_post_types() {
        return array_column(get_post_types_object(), 'post_type');
    }
    function get_current_post_type($postType = '') {
        $currentPostTypeObject = [];
        if ($postType != '') {
            foreach (get_post_types_object() as $key => $postTypeObject) {
                if ($postTypeObject['post_type'] == $postType) {
                    $currentPostTypeObject = $postTypeObject;
                }
            }
        }
        return $currentPostTypeObject;
    }
    
    