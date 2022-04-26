<?php
    use App\Models\Term;
    use App\Models\TermRelationship;

    if (!function_exists('data_table')) {

        function data_table($data) {
            $columnsF = [];
            foreach ($data['columns'] as $key => $column) {
                $columnsF[] = ['data' => stripos($column, 'as') != false ? str_replace(' ', '', substr($column, stripos($column, 'as')+2)) : $column];
            }
            $response['frontend'] = "<div class='card shadow mb-4'>
            <div class='card-header py-3'>
                <h6 class='m-0 font-weight-bold text-primary'>".@$data['title']."</h6>
            </div>
            <div class='card-body'><table class='c-datatable table table-bordered' data-columns='".json_encode($columnsF)."' data-url='".route('pages-ajax')."'  >
            <thead>
                <tr>";
            foreach ($data['tColumns'] as $key => $tColumn) {
                if(is_image($tColumn)) {
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

    }

    

    if (!function_exists('is_image')) {

        function is_image($image) {
            $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];

            $explodeImage = explode('.', $image);
            $extension = end($explodeImage);
            // var_dump($extension);
            // var_dump(in_array($extension, $imageExtensions)); 
            return in_array($extension, $imageExtensions);
        }

    }

    if (!function_exists('word_format')) {

        function word_format($name, $type = null) {

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

    }

    

    /*
    if (!function_exists('get_parent_child_categories_dropdown')) {

        function get_parent_child_categories_dropdown($id) {
            global $html, $dash;
            if ($id != '') {
                $term1 = Term::query();
                if (\c_user()->user_type != 'admin') {
                    $term1->where(['user_id' => \c_user()->ID]);
                }
                $term1 = $term1->where(['parent_id' => $id])->get();
                
                if ($term1->count() > 0) {
                    $dash .= '-';
                    foreach($term1 as $cat) {
                        $html .= '<option value="'.$cat->id.'"> '.$dash.' '.$cat->title.'</option>';
                        get_parent_child_categories_dropdown($cat->id);
                    }
                }
            }

            return $html;                           
        }

    }

    if (!function_exists('get_parent_child_categories_table')) {

        function get_parent_child_categories_table($id = 0) {
            global $html2;
            global $dash2;
            if ($id != '') {
                $name = 'term';
                $term1 = Term::query();
                if (\c_user()->user_type != 'admin') {
                    $term1->where(['user_id' => \c_user()->ID]);
                }
                $term1 = $term1->where(['parent_id' => $id])->get();
                
                if ($term1->count() > 0) {
                    $dash2 .= '-';
                    
                    foreach($term1 as $cat) {
                        
                        $html2 .= '<tr>
                                    <td><input type="checkbox" name="action_ids[]" value="'.$cat->id.'"></td>
                                    <td>'.$cat->id.'</td>
                                    <td> '.$dash2.' '.$cat->title.'</td>
                                    <td>'.$cat->description.'</td>
                                    <td><img src="'.get_image($cat->featured_image).'" width="50"></td>
                                    <td>'.$cat->created_at->diffForHumans().'</td>
                                    <td class="action">';
                                    $html2 .= '<a href="'.route('edit-'.word_format($name), $cat->id).'" class="edit-record">
                                        <i class="fa fa-pencil-alt"></i>
                                        </a>';
                                    $html2 .= '<a href="'.route('delete-'.word_format($name), $cat->id).'" 
                                    class="danger-delete" >
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr> ';
                        
                        get_parent_child_categories_table($cat->id);
                    }
                }

            }

            return $html2;                           
        }

    }
    */

    if (!function_exists('term_html')) {

        function term_html($cat, $dash2) {
            $name = 'term';
            return '<tr>
                                    <td><input type="checkbox" name="action_ids[]" value="'.$cat->id.'"></td>
                                    <td>'.$cat->id.'</td>
                                    <td> '.$dash2.' '.$cat->title.'</td>
                                    <td>'.$cat->description.'</td>
                                    <td><img src="'.get_image($cat->featured_image).'" width="50"></td>
                                    <td>'.get_admin_panel_dates($cat).'</td>
                                    <td class="action">
                                        <a href="'.route('edit-'.word_format($name), $cat->id).'" class="edit-record">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <a href="'.route('delete-'.word_format($name), $cat->id).'" class="danger-delete" >
                                                <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr> ';
        }

    }

    if (!function_exists('get_parent_child_categories_dropdown')) {

        function get_parent_child_categories_dropdown($id) {
            $html = '';
            $dash = '';
            if ($id != '') {
                $term1 = Term::query();
                $term1->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->get();
                $term1 = $term1->where(['term_taxonomy.parent' => $id])->get();
                if ($term1->count() > 0) {
                    $dash = '-';
                    foreach($term1 as $cat) {
                        $html .= '<option value="'.$cat->term_id.'"> '.$dash.' '.$cat->name.'</option>';
                        $term2 = Term::query();
                        $term2->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->get();
                        $term2 = $term2->where(['term_taxonomy.parent' => $cat->term_id])->get();
                        if ($term2->count() > 0) {
                            $dash = '--';
                            foreach($term2 as $cat2) {
                                $html .= '<option value="'.$cat2->term_id.'"> '.$dash.' '.$cat2->name.'</option>';
                                $term3 = Term::query();
                                $term3->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->get();
                                $term3 = $term3->where(['term_taxonomy.parent' => $cat2->term_id])->get();
                                if ($term3->count() > 0) {
                                    $dash = '---';
                                    foreach($term3 as $cat3) {
                                        $html .= '<option value="'.$cat3->term_id.'"> '.$dash.' '.$cat3->name.'</option>';
                                        $term4 = Term::query();
                                        $term4->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->get();
                                        $term4 = $term4->where(['term_taxonomy.parent' => $cat3->term_id])->get();
                                        if ($term4->count() > 0) {
                                            $dash = '----';
                                            foreach($term4 as $cat4) {
                                                $html .= '<option value="'.$cat4->term_id.'"> '.$dash.' '.$cat4->name.'</option>';
                                                $term5 = Term::query();
                                                $term5->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->get();
                                                $term5 = $term5->where(['term_taxonomy.parent' => $cat4->term_id])->get();
                                                if ($term5->count() > 0) {
                                                    $dash = '-----';
                                                    foreach($term5 as $cat5) {
                                                        $html .= '<option value="'.$cat5->term_id.'"> '.$dash.' '.$cat5->name.'</option>';
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

    }

    if (!function_exists('get_parent_child_categories_table')) {

        function get_parent_child_categories_table($id = 0) {
            // global $html2;
            // global $dash2;
            $html2 = '';
            $dash2 = '';
            if ($id != '') {
                $name = 'term';
                $term1 = Term::query();
                $term1->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id');
                $term1 = $term1->where(['term_taxonomy.parent' => $id])->get();
                
                if ($term1->count() > 0) {
                    $dash2 = '-';
                    
                    foreach($term1 as $cat) {
                        
                        $html2 .= term_html($cat, $dash2);
                        $term2 = Term::query();
                        $term2->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id');
                        $term2 = $term2->where(['term_taxonomy.parent' => $cat->term_id])->get();
                            
                        if ($term2->count() > 0) {
                            $dash2 = '--';
                            
                            foreach($term2 as $cat2) {
                                
                                $html2 .= term_html($cat2, $dash2);

                                $term3 = Term::query();
                                $term3->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id');
                                $term3 = $term3->where(['term_taxonomy.parent' => $cat2->term_id])->get();
                                // var_dump($term3->count()); die;
                                if ($term3->count() > 0) {
                                    $dash2 = '---';
                                    // dd($term3->toArray());
                                    foreach($term3 as $cat3) {
                                        
                                        $html2 .= term_html($cat3, $dash2);

                                        $term4 = Term::query();
                                        $term4->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id');
                                        $term4 = $term4->where(['term_taxonomy.parent' => $cat3->term_id])->get();
                                        // var_dump($term4->count()); die;
                                        if ($term4->count() > 0) {
                                            $dash2 .= '-';
                                            
                                            foreach($term4 as $cat4) {
                                                $html2 .= term_html($cat4, $dash2);
                                                $term5 = Term::query();
                                                $term5->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id');
                                                $term5 = $term5->where(['term_taxonomy.parent' => $cat4->term_id])->get();
                                                if ($term5->count() > 0) {
                                                    $dash2 .= '-';
                                                    
                                                    foreach($term5 as $cat5) {
                                                        $html2 .= term_html($cat5, $dash2);
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

    }

    

    if (!function_exists('get_term_string_format')) {

        function get_term_string_format($id) {
            
            return array_column(Term::select(['name'])
            ->whereIn(
                'term_id', TermRelationship::select(['term_taxonomy_id'])->where(['object_id' => $id])->get()->pluck('term_taxonomy_id')
            )->get()->toArray(), 'name');
        }

    }

    if (!function_exists('get_user_image')) {

        function get_user_image($img) {
            $imgPath = public_path('/assets/images/'.$img);
            $url = File::exists($imgPath) && File::isFile($imgPath) ? asset('public/assets/images/'.$img) : asset('public/assets/images/demo_profile.svg');
            return $url;
        }

    }

    if (!function_exists('get_image')) {

        function get_image($img) {
            $url = '';
            if($img != '' && (File::exists(public_path('/assets/images/'.$img)) && File::isFile(public_path('/assets/images/'.$img)))) {
                $url = asset('public/assets/images/'.$img);
            }
            else {
                $url = asset('public/assets/images/placeholder-img.jpg');
            }
            return $url;
        }

    }

    if (!function_exists('c_user')) {

        function c_user() {
            return Auth::user();
        }

    }

    
    if (!function_exists('get_roles_permissions')) {

        function get_roles_permissions() {
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

    }

    if (!function_exists('get_image_extensions')) {

        function get_image_extensions($type = 'string') {
            $ext = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'webp'];
            if ($type == 'string') 
                $ext = implode(',', $ext);
            return $ext;
        }

    }

    
    if (!function_exists('get_admin_body_classes')) {

        function get_admin_body_classes($class = '') {
            $classes = ''; 
            if (Route::is('edit-*')  ) {
                $classes .= ' edit-page';
            }
            $classes .= ' '.$class;
            return $classes;
        }

    }

    if (!function_exists('get_admin_body_attributes')) {

        function get_admin_body_attributes($attribute = []) {
            $attributes = ''; 
            
            foreach ($attribute as $key => $att) {
                $attributes .= ' '.$key.'='.$att.'';
            }
            
            return $attributes;
        }

    }

    
    if (!function_exists('get_admin_post_type_url')) {

        function get_admin_post_type_url($routeParams, $postType) {
            $route = null;
            if (array_value($routeParams, 'name') != null && array_value($routeParams, 'id') != null) {
                $route = route(array_value($routeParams, 'name'), array_value($routeParams, 'id')).'?post_type='.$postType;
            }
            elseif(array_value($routeParams, 'name') != null) {
                $route = route(array_value($routeParams, 'name')).'?post_type='.$postType;
            }
            return $route;
        }

    }
    
    if (!function_exists('get_admin_taxonomy_url')) {

        function get_admin_taxonomy_url($routeParams, $taxonomy = '', $postType = '') {
            $route = null;
            
            if($taxonomy == '') {
                return cms_error('Second argument must be entered');
            }
            elseif($postType == '') {
                return cms_error('Third argument must be entered');
            }
            elseif (array_value($routeParams, 'name') != null && array_value($routeParams, 'id') != null) {
                $route = route(array_value($routeParams, 'name'), array_value($routeParams, 'id')).'?taxonomy='.$taxonomy.'&post_type='.$postType;
            }
            elseif(array_value($routeParams, 'name') != null) {
                $route = route(array_value($routeParams, 'name')).'?taxonomy='.$taxonomy.'&post_type='.$postType;
            }
            return $route;
        }

    }

    if (!function_exists('get_admin_panel_dates')) {

        function get_admin_panel_dates($record) {
            
            $response = '<div class="a-p-date-wrap">';
            if ($record) {
                if ($record->created_at != '') {
                    $response .= '<div class="a-p-created-at">Created At : '.get_admin_panel_datetime($record->created_at).'</div>';
                }
                if ($record->updated_at != '' && $record->updated_at != $record->created_at) {
                    $response .= '<div class="a-p-updated-at">Updated At : '.get_admin_panel_datetime($record->updated_at).'</div>';
                }

                // if ($record->created_at == $record->updated_at ) {
                //     $response .= '<div class="a-p-updated-at">'.\Carbon\Carbon::parse($record->created_at)->diffForHumans(\Carbon\Carbon::now()).'</div>';
                // }
                // else {
                //     $response .= '<div class="a-p-updated-at">'.\Carbon\Carbon::parse($record->updated_at)->diffForHumans(\Carbon\Carbon::now()).'</div>';
                // }
            }
            $response .= '</div>';
            return $response;
        }

    }

    if (!function_exists('get_admin_panel_post_type_dates')) {

        function get_admin_panel_post_type_dates($record) {
            
            $response = '<div class="a-p-date-wrap">';
            if ($record) {
                if ($record->post_modified != '' && $record->post_modified != $record->post_date) {
                    $response .= '<div class="a-p-updated-at">Last Modified <p>'.get_admin_panel_datetime($record->updated_at).'</p></div>';
                }
                else if ($record->post_date != '') {
                    $response .= '<div class="a-p-created-at">Published <p>'.get_admin_panel_datetime($record->created_at).'</p></div>';
                }
                

                // if ($record->created_at == $record->updated_at ) {
                //     $response .= '<div class="a-p-updated-at">'.\Carbon\Carbon::parse($record->created_at)->diffForHumans(\Carbon\Carbon::now()).'</div>';
                // }
                // else {
                //     $response .= '<div class="a-p-updated-at">'.\Carbon\Carbon::parse($record->updated_at)->diffForHumans(\Carbon\Carbon::now()).'</div>';
                // }
            }
            $response .= '</div>';
            return $response;
        }

    }

    if (!function_exists('get_admin_panel_date')) {

        function get_admin_panel_date() {
            return get_option('admin_panel_date_format');
        }

    }

    

    if (!function_exists('get_admin_panel_time')) {

        function get_admin_panel_time() {
            return get_option('admin_panel_time_format');
        }

    }
    
    if (!function_exists('get_date_formats')) {

        function get_date_formats() {
            $formats = [
                'F j, Y',
                'Y-m-d',
                'm/d/Y',
                'd/m/Y',
            ];
            return $formats;
        }

    }
    

    if (!function_exists('get_time_formats')) {

        function get_time_formats() {
            $formats = [
                'g:i a',
                'g:i A',
                'H:i',
            ];
            return $formats;
        }

    }

    
    if (!function_exists('check_own_record_or_has_permission')) {

        function check_own_record_or_has_permission($model, $req) {
            $result = true;
            if (c_user()->is_super_admin != 1) {
                $id = $req->route()->parameter('id');
                if ($id != '') {
                    if ($model::where(['id' => $id, 'user_id' => c_user()->ID])->count() == 0) {
                        $result = false;
                    }
                }
                else if($req->input('action_ids')) {
                    if ($model::whereIn('id', $req->input('action_ids'))->where(['user_id' => c_user()->ID])->count() == 0) {
                        $result = false;
                    }
                }
            }
            return $result;
        }

    }

    
    if (!function_exists('code_execute_time')) {

        function code_execute_time($rus, $ru) {
            $index = 'stime';
            return (($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
             -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000))) / 100;
        }

    }

    if (!function_exists('array_value')) {

        function array_value($array, $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                return $array[$key];
            }
        }

    }

    

    if (!function_exists('get_admin_panel_datetime')) {

        function get_admin_panel_datetime($date) {
            $dateC = \Carbon\Carbon::parse($date)->format(get_option('admin_panel_date_format'));
            $timeC = \Carbon\Carbon::parse($date)->format(get_option('admin_panel_time_format'));
            return $dateC.' at '.$timeC;
        }

    }
    function ___($string) {
        return $string;
    }

    
?>