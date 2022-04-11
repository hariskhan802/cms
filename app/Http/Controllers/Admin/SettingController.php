<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Option;


class SettingController extends Controller
{
    public function general_settings(Request $req) {
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'site_title' => 'required',
                'tagline' => 'required',
                'site_address_url' => 'required',
                'administration_email_address' => 'required',
                
            ];
            unset($data['_token'], $data['submit']);
            if (@$data['membership'] == 'on') {
                $vArgs['new_user_default_role'] = 'required';      
            }

            $validated = Validator::make($data, $vArgs);

            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }
            foreach ($data as $option_name => $option_value) {
                if ($option_value != '' ) {
                    __update_option($option_name, $option_value);
                }
            }
            $response['status'] = 'success';
            $response['message'] = 'You have updated successfully';
            return $response;

        }
        else {
            $name = 'general settings';

            return view('Admin.Settings.general-settings', ['name' => $name, 'roles' => __get_roles()]);
        }
    }

    public function roles_settings(Request $req) {
        if ($req->isMethod('post')) {
            $data = $req->all();
            foreach (\App\Models\Role::select(['role'])->get()->pluck('role')->toArray() as $key => $role) {
            
                \App\Models\Role::where(['role' => $role])->update(['permissions' => @$data['roles'][strtolower($role)] ? serialize(array_keys(@$data['roles'][strtolower($role)])) : '']);
            }
            $response['status'] = 'success';
            $response['message'] = 'You have updated successfully';
            return $response;
        }
        else {
            $name = 'roles settings';
            return view('Admin.Settings.roles-settings', ['name' => $name, 'roles' => __get_roles()]);
        }
    }
}
