<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Validator;

class RoleController extends Controller
{
    private function add_edit_and_listing($req) {
        $role1 = Role::query();
        $role2 = Role::query();
        $name = 'role';
        $totalRecords = $role1->count();
        if ($req->input('search')) {
            $role2->where('roles.role', 'like', "%{$req->input('search')}%");
        }
        
        return view('Admin.Role.index', ['name' => $name, 'totalRecords' => $totalRecords, 'data' => $role2->select(['roles.id', 'roles.role', 'roles.permissions', 'roles.created_at', 'roles.updated_at'])->orderBy('roles.id', 'ASC')->paginate(10)]);
    }
    public function index(Request $req) {
        return $this->add_edit_and_listing($req);
    }
    public function add(Request $req) {
        $data = $req->all();
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'role' => 'required|min:2',
        ]);
        $data['user_id'] = c_user()->ID;
        $data['post_status'] = 'drafted';
        if ($data['_status'] == 'Publish') {
            $data['post_status'] = 'published';
        }
        if ($validated->fails()) {
            $response['errors'] = $validated->getMessageBag()->toArray();
            $response['status'] = 'fail';
            return $response;
        }
        $data['permissions'] = serialize(array_keys($req->input('permissions')));
        if(Role::create($data)) {
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (c_user()->is_super_admin != 1) {
            if (Role::where(['id' => $id,])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $role = Role::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'role' => 'required|min:2',
            ];
            $validated = Validator::make($data, $vArgs);
            
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }
            $data['permissions'] = serialize(array_keys($req->input('permissions')));
            if($role->update($data)) {
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                $response = ['status' => 'success', 'item' => $role];
                return $response;
            }
            else {
                return $this->add_edit_and_listing($req);
            }
        }

    }
    public function delete($id = null, Request $req) {
        if ($id == 1 || $id == 2) {
            return back()->with('errormsg', 'You can not delete this role');
        }
        if ($id) {
            if (Role::where(['id' => $id])->count() > 0) {
                Role::where(['id' => $id])->delete();
            }
            return back()->with('msg', 'Delete successfully');
        }
        else {
            if ($req->input('rec_action') == 'delete') {
                if (in_array(1, $req->input('action_ids')) || in_array(2, $req->input('action_ids'))) {
                    return back()->with('errormsg', 'You can not delete this role');
                }
                if (Role::whereIn('id', $req->input('action_ids'))->count() > 0) {
                    Role::whereIn('id', $req->input('action_ids'))->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

}
