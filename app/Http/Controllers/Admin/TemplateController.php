<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Role;
use Validator;
use File;

class TemplateController extends Controller
{
    public function add_edit_and_listing($req) {
        $template1 = Template::query();
        $template2 = Template::query();
        $name = 'template';
        $totalRecords = $template1->count();
        if ($req->input('search')) {
            $template2->where('templates.title', 'like', "%{$req->input('search')}%");
        }
        
        return view('Admin.Template.index', ['name' => $name, 'roles' => Role::select(['id', 'role'])->get(), 'totalRecords' => $totalRecords, 'data' => $template2->select(['templates.id', 'templates.title', 'templates.created_at', 'templates.updated_at'])->orderBy('templates.id', 'DESC')->paginate(10)]);
    }
    public function index(Request $req) {
        return $this->add_edit_and_listing($req);        
    }
    public function add(Request $req) {
        $data = $req->all();
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'title' => 'required|unique:templates',
            'content' => 'required',
        ]);
        
        if ($validated->fails()) {
            $response['errors'] = $validated->getMessageBag()->toArray();
            $response['status'] = 'fail';
            return $response;
        }
        
        if(Template::create($data)) {
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (__c_user()->is_super_admin != 1) {
            if (Template::where(['id' => $id])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $template = Template::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'title' => 'required|unique:templates',
                'content' => 'required',
            ];
            if ($template->title == $data['title']) {
                unset($vArgs['title']);
            }

            $validated = Validator::make($data, $vArgs);
            
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }

            if($template->update($data)) {
                
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                $response = ['status' => 'success', 'item' => $template];
                return $response;
            }
            else {
                return $this->add_edit_and_listing($req);
            }
        }

    }
    public function delete($id = null, Request $req) {
        if (__c_user()->is_super_admin != 1) {
            if ($id) {
                if (Template::where(['id' => $id,])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Template::whereIn('id', $req->input('action_ids'))->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }
        if ($id) {
            if (Template::where(['id' => $id, ])->count() > 0) {
                Template::where(['id' => $id, ])->delete();
            }
            return back()->with('msg', 'Delete successfully');
        }
        else {
            
            if ($req->input('rec_action') == 'delete') {
                if (Template::whereIn('id', $req->input('action_ids'))->count() > 0) {
                    Template::whereIn('id', $req->input('action_ids'))->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    public function restore($id = null, Request $req) {
        if (__c_user()->is_super_admin != 1) {
            if ($id) {
                if (Template::where(['id' => $id, 'user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Template::whereIn('id', $req->input('action_ids'))->where(['user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }

        if ($id) {
            if (Template::where(['id' => $id, 'post_status' => 'trashed'])->count() > 0) {
                Template::where(['id' => $id, 'post_status' => 'trashed'])->update(['post_status' => 'published']);
                return back()->with('msg', 'Restored successfully');
            }
        }
        else {
            if ($req->input('rec_action') == 'restore') {
                if (Template::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->count() > 0) {

                    Template::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->update(['post_status' => 'published']);
                    return back()->with('msg', 'Restored successfully');
                }
            }
        }
    }
}
