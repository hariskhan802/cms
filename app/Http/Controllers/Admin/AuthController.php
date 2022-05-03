<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use File;
use Image;

class AuthController extends Controller
{
    
    public function login(Request $req) {

        if ($req->isMethod('post')) {
            $data = $req->all();
            $validated = \Validator::make($data, [
                'email' => 'required',
                'password' => 'required',
            ]);
            $redirectTo = $req->input('redirect_to') != '' ? $req->input('redirect_to') : route('dashboard');
            
            if ($validated->fails()) {
                return back()->withErrors($validated)->withInput();
            }
            // var_dump($data['email']);
            // var_dump(bcrypt($data['password']));
            // die;
            /* if (Auth::attempt(['user_email' => $data['email'], 'user_pass' =>  bcrypt($data['password'])], $req->input('rememberme'))) {
                if ($req->query('redirect')){
                    return redirect($req->query('redirect'));
                }
                return redirect($redirectTo);
            }
            else {
                return back()->with('errormsg', 'Email or password is incorrect!');
            } */
            $userInfo = \App\Models\User::where(['user_email' => $data['email']])->first();

            // dd($userInfo);
            if ($userInfo) {
                if (\Hash::check($data['password'], $userInfo->user_pass)) {
                    $userInfo = $userInfo->toArray();
                    session()->put($userInfo);
                    // print_r($req->query('redirect')); die;
                    if ($req->query('redirect')){

                        return redirect($req->query('redirect'));
                    }
                    return redirect($redirectTo);
                }
                else{
                    return back()->with('message', 'Email or password is incorrect!');

                }
            }
            else{
                return back()->with('message', 'Email or password is incorrect!');
            }
        }
        else {
            return view('Admin.Auth.login');
        }
    }
    public function logout(Request $req) {
        session()->pull('ID');
        session()->pull('user_login');
        session()->pull('user_nicename');
        session()->pull('user_email');
        session()->pull('user_url');
        session()->pull('user_registered');
        session()->pull('user_activation_key');
        session()->pull('user_status');
        session()->pull('display_name');
        session()->pull('is_super_admin');
        
        return redirect(route('admin-login'));
    }

    public function profile(Request $req) {
        $user = \App\Models\User::findOrfail(array_value(c_user(), 'ID'));
        if ($req->isMethod('post')) {
            $data = $req->all();
            $vArgs = [];
            if ($data['submit'] == 'Update') {
                $vArgs = [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'image' => 'required||file|max:1000|mimes:'.get_image_extensions('string'),
                ];
                if ($data['_image'] == $user->image) 
                    $vArgs['image'] = 'file|max:1000|mimes:'.get_image_extensions('string');
                if ($user->email == $data['email']) 
                    $vArgs['email'] = 'required|email';
            }
            else if($data['submit'] == 'Change Password') {
                $vArgs = [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6|same:password_confirmation',
                    'password_confirmation' => 'required',
                    // 'confirm_password' => 'required',
                ];
            }
            
            if (@$data['current_password'] != '' && !\Hash::check(@$data['current_password'], $user->password)) {
                $response['errors'] = ['current_password' => 'Password is incorrect'];
                $response['status'] = 'fail';
                return $response;
            }
            $validated = \Validator::make($data, $vArgs);
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }

            if (@$data['_image'] != '') 
                $data['image'] = $data['_image'];

            if (@$data['new_password'] != '')
                @$data['password'] = bcrypt(@$data['new_password']);

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
            if (\App\Models\User::findOrfail(array_value(c_user(), 'ID'))->update($data)) {
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            $name = 'profile';

            return view('Admin.Auth.profile', ['name' => $name,]);
        }
    }

    public function settings(Request $req) {
        
    }
    
}
