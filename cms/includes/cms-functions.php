<?php
    use App\Models\Option;
    use App\Models\Role;
    use App\Models\Postmeta;

    if (!function_exists('get_option')) {

        function get_option($optionName) {
            $optionValue =  Option::select(['option_value'])->where(['option_name' => $optionName])->pluck('option_value')->first();
            return  $optionValue == '[]' ? '' : $optionValue;
        }

    }

    if (!function_exists('add_option')) {

        function add_option($optionName, $optionValue) {
            $success = false;
            if(Option::where('option_name', $optionName)->count() > 0 ) {
                $success = false;
            }
            else if(Option::create(['option_name' => $optionName, 'option_value' => $optionValue])) {
                $success = true;
            }
            return $success;
        }

    }

    if (!function_exists('update_option')) {

        function update_option($optionName, $optionValue) {
            $success = false;
            
            if(Option::where('option_name', $optionName)->count() > 0 ) {
                if (Option::where(['option_name' => $optionName])->update(['option_value' => $optionValue])) {
                    $success = true;
                }
                
            }
            else if(Option::create(['option_name' => $optionName, 'option_value' => $optionValue])) {
                $success = true;
            }
            return $success;
        }

    }

    if (!function_exists('get_post_meta')) {

        function get_post_meta($postID = '', $metaKey = '', $single = false) {
            if ($postID == '') {
                return cms_error('First argument must be entered');
            }
            $postMeta = Postmeta::query();
            $postMeta->where('post_id', $postID);
            $columns = $metaKey == '' ? ['meta_key', 'meta_value'] : ['meta_value'];
            
            $metaValue =  $postMeta->select($columns)->when($metaKey != '', function($q) use($metaKey){
                return $q->where('meta_key', $metaKey);
            });
            if ($metaKey == '') 
                $metaValue = $metaValue->get()->toArray();
            
            else {
                if($single == true) 
                    $metaValue = $metaValue->pluck('meta_value')->first();
                else 
                    $metaValue = $metaValue->get()->toArray();
                
            }
            return  $metaValue == '[]' ? '' : $metaValue;
        }

    }



    if (!function_exists('update_post_meta')) {

        function update_post_meta($postID, $metaKey, $metaValue) {
            $success = false;
            if(Postmeta::where('meta_key', $metaKey)->count() > 0 ) {
                if (Postmeta::where(['post_id' => $postID, 'meta_key' => $metaKey])->update(['meta_value' => $metaValue])) {
                    $success = true;
                }
                
            }
            else if(Postmeta::create(['post_id' => $postID, 'meta_key' => $metaKey, 'meta_value' => $metaValue])) {
                $success = true;
            }
            return $success;
        }

    }


    if (!function_exists('get_roles')) {

        function get_roles() {
            return Role::select(['id', 'role', 'permissions'])->get();
        }

    }



    if (!function_exists('cms_error')) {

        function cms_error($error) {
            return '<div class="cms-error"><h6>'.$error.'</h6></div>';
        }

    }

    
    if (!function_exists('add_action')) {

        function add_action($hookName = '', $callback = '', $priority = 10) {
            global $cmsHooks;
            $registered_hooks = ['init', 'wp_head', 'wp_footer'];
            if (($hookName == '' || $callback == '') || ($hookName == '' && $callback == '') ) {
                echo cms_error('First and Second Arguments can not be empty!');
                // return false;
            }
            else if (is_string($callback) && !function_exists($callback)) {
                echo cms_error('Call back function is not exist!');
                // return false;
            }
            else {
                $cmsHooks[] = [
                    'hook_name' => $hookName,
                    'callback' => $callback,
                    'priority' => $priority,
                ];
            }
        }

    }

    if (!function_exists('do_action')) {

        function do_action($hookName ='') {
            global $cmsHooks;
            $actionRespoonse = '';
            if ($cmsHooks && count($cmsHooks) > 0) {
                $priority = array_column($cmsHooks, 'priority');
                array_multisort($priority, SORT_ASC, $cmsHooks);
                foreach ($cmsHooks as $key => $cmsHook) {
                    if (isset($cmsHook['hook_name']) && !empty($cmsHook['hook_name'])) {
                        if ($cmsHook['hook_name'] == $hookName) {
                            $callback = $cmsHook['callback'];
                            $actionRespoonse .= $callback();
                        }
                    }
                }
                
            }
            return $actionRespoonse;
        }

    }

    if (!function_exists('register_post_type')) {

        function register_post_type($postType = null, $args = []) {
            global $cmsPostTypes;
            // var_dump($postType); die;
            if (!is_string($postType) || $postType == '') {
                return cms_error('First argument must be entered');
            }
            else{
                $args['post_type'] = $postType;
                $args['taxonomies'] = [];
                $cmsPostTypes[] = $args;
            }
        }

    }

    if (!function_exists('get_post_types_object')) {

        function get_post_types_object() {
            global $cmsPostTypes, $cmsTaxonomies;
            // print_r($cmsPostTypes[1]['post_type']); die;
            // die;
            if(is_array($cmsPostTypes)){
                for ($i=0; $i < count($cmsTaxonomies); $i++) {
                    // print_r(); die; 
                    // print_r(array_value((array_value($cmsPostTypes, $i)), 'post_type')); 
                    // print_r($cmsTaxonomies); 
                    // die;
                    if(in_array(array_value((array_value($cmsPostTypes, $i)), 'post_type'), array_column($cmsPostTypes, 'post_type'))) {
                        $cmsPostTypes[$i]['taxonomies'][$cmsTaxonomies[$i]['taxonomy']] =  $cmsTaxonomies[$i];
                    }
                    
                }
                // print_r($cmsPostTypes); die;
                $menuPosition = array_column($cmsPostTypes, 'menu_position');
                array_multisort($menuPosition, SORT_ASC, $cmsPostTypes);
            }
            // print_r($cmsPostTypes); die;
            return $cmsPostTypes;
        }

    }

    if (!function_exists('get_post_types')) {

        function get_post_types() {
            if(is_array(get_post_types_object())){
                return array_column(get_post_types_object(), 'post_type');
            }
        }

    }

    if (!function_exists('get_current_post_type')) {

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

    }
    

    if (!function_exists('register_taxonomy')) {

        function register_taxonomy($taxonomy = '', $postType = '', $args = []) {
            global $cmsTaxonomies;
            if ($taxonomy == '') {
                return cms_error('First argument must be entered');
            }
            else if ($postType == '') {
                return cms_error('Second argument must be entered');
            }
            else {
                $args['taxonomy'] = $taxonomy;
                $args['post_type'] = $postType;
                $cmsTaxonomies[] = $args;
            }
            // print_r($cmsTaxonomies); die;
            return $cmsTaxonomies;
        }

    }