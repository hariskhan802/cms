<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Validator;
use File;
use Image;

class UserController extends Controller
{

    public function add_edit_and_listing($req) {
        $user1 = User::query();
        $user2 = User::query();
        $name = 'user';
        $totalRecords = $user1->count();
        if ($req->input('search')) {
            $user2->where('users.display_name', 'like', "%{$req->input('search')}%")->orWhere('users.user_email', 'like', "%{$req->input('search')}%");
        }
        /* $dom = new DOMDocument;
        $finder = new \Illuminate\View\FileViewFinder(app()['files'], array(resource_path().'/views'));
        $endpoint = \Request::url();
        $client = new \GuzzleHttp\Client();

        $response = $client->get($endpoint, [
                        GuzzleHttp\RequestOptions::JSON => ['key1' => 'test'],
                    ]);

        var_dump($response);
        $dom->loadHTML('');
        $imgs = $dom->getElementsByTagName('img');
            print_r($imgs); die;
        foreach ($imgs as $img) {
            $img->setAttribute('loading', 'lazy');
        }
        $html = $dom->saveHTML();
        return $html; */
        return view('Admin.User.index', ['name' => $name, 'roles' => Role::select(['id', 'role'])->get(), 'totalRecords' => $totalRecords, 'data' => $user2->select(['users.ID', 'users.display_name', 'users.user_email', 'users.is_super_admin', 'users.user_registered'])->orderBy('users.id', 'DESC')->paginate(10)]);
    }
    public function index(Request $req) {
        return $this->add_edit_and_listing($req);        
    }
    public function add(Request $req) {
        $data = $req->all();
        // print_r($data); die;
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            'image' => 'required||file|max:1000|mimes:'.get_image_extensions('string'),
        ]);
        
        if ($validated->fails()) {
            $response['errors'] = $validated->getMessageBag()->toArray();
            $response['status'] = 'fail';
            return $response;
        }
        $image = $req->file('image');
        $input['imagename'] = 'img-'.uniqid().time().'.'.$image->extension();
        $path = public_path('/assets/images');
        if(!File::exists($path)){
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        $img = Image::make($image->path());
        $img->save($path.'/'.$input['imagename'], 50);
        $data['image'] = $input['imagename'];
        $data['password'] = bcrypt($input['password']);
        if(User::create($data)) {
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (array_value(c_user(), 'is_super_admin') != 1) {
            if (User::where(['id' => $id])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $user = User::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            // print_r($data); die;
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'name' => 'required',
                'email' => 'required|unique:users',
                'image' => 'required||file|max:1000|mimes:'.get_image_extensions('string'),
            ];
            if ($data['_image'] == $user->image)
                $vArgs['image'] = 'file|max:1000|mimes:'.get_image_extensions('string');
            
            if ($user->email == $data['email']) 
                $vArgs['email'] = 'required|email';

            $validated = Validator::make($data, $vArgs);
            
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }
            $data['menu_order'] = 0;
            if (isset($data['password'] )) {
                unset($data['password'] );
            }
            $data['image'] = $data['_image'];
            if ($req->file('image')) {
                File::delete('public/assets/images/'.$data['_image']);
                $image = $req->file('image');
                $input['imagename'] = 'img-'.uniqid().time().'.'.$image->extension();
                $path = public_path('/assets/images');
                if(!File::exists($path)){
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $img = Image::make($image->path());
                $img->save($path.'/'.$input['imagename'], 50);
                $data['image'] = $input['imagename'];
            }

            if($user->update($data)) {
                
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                $response = ['status' => 'success', 'item' => $user];
                return $response;
            }
            else {
                return $this->add_edit_and_listing($req);
            }
        }

    }
    public function delete($id = null, Request $req) {
        if (array_value(c_user(), 'is_super_admin') != 1) {
            if ($id) {
                if (User::where(['id' => $id,])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (User::whereIn('id', $req->input('action_ids'))->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }
        if ($id) {
            if (User::where(['id' => $id, ])->count() > 0) {
                $fImg = User::select('image')->where(['id' => $id])->first()->toArray()['image'];
                File::delete('public/assets/images/'.$fImg);
                User::where(['id' => $id, ])->delete();
            }
            return back()->with('msg', 'Delete successfully');
        }
        else {
            
            if ($req->input('rec_action') == 'delete') {
                if (User::whereIn('id', $req->input('action_ids'))->count() > 0) {
                    $fImgs = User::select('image')->whereIn('id', $req->input('action_ids'))->get()->toArray();
                    foreach ($fImgs as $key => $fImg) {
                        File::delete('public/assets/images/'.$fImg['image']);
                    }
                    User::whereIn('id', $req->input('action_ids'))->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    public function restore($id = null, Request $req) {
        if (array_value(c_user(), 'is_super_admin') != 1) {
            if ($id) {
                if (User::where(['id' => $id, 'user_id' => array_value(c_user(), 'ID')])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (User::whereIn('id', $req->input('action_ids'))->where(['user_id' => array_value(c_user(), 'ID')])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }

        if ($id) {
            if (User::where(['id' => $id, 'post_status' => 'trashed'])->count() > 0) {
                User::where(['id' => $id, 'post_status' => 'trashed'])->update(['post_status' => 'published']);
                return back()->with('msg', 'Restored successfully');
            }
        }
        else {
            if ($req->input('rec_action') == 'restore') {
                if (User::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->count() > 0) {

                    User::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->update(['post_status' => 'published']);
                    return back()->with('msg', 'Restored successfully');
                }
            }
        }
    }
    
}
