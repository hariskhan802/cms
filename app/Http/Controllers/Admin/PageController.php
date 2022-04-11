<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Validator;
use File;
use Image;

class PageController extends Controller
{
    private function add_edit_and_listing($req) {
        $page1 = Page::query();
        $page2 = Page::query();
        $name = 'page';
        $totalRecords = $page1->where('post_status', '!=', 'trashed')->count();
        if ($req->input('search')) {
            $page2->where('pages.title', 'like', "%{$req->input('search')}%");
        }
        if (__c_user()->is_super_admin != 1) {
            $page2->where('pages.user_id', '=', __c_user()->id);
        }
        if ($req->input('status') == '') {
            $page2->where(['post_status' => 'published'])->orWhere(['post_status' => 'drafted']);
        }
        else if ($req->input('status') == 'published') {
            $page2->where(['post_status' => 'published']);
        }
        else if ($req->input('status') == 'drafts') {
            $page2->where(['post_status' => 'drafted']);
        }   
        else if ($req->input('status') == 'trash') {
            $page2->where(['post_status' => 'trashed']);
        }
        return view('Admin.Page.index', ['name' => $name, 'totalRecords' => $totalRecords, 'data' => $page2->select(['pages.id', 'pages.title', 'pages.featured_image', 'templates.title as template', 'pages.created_at'])->leftjoin('templates', 'templates.id', '=', 'pages.template_id')->orderBy('pages.id', 'DESC')->paginate(10)]);
    }
    public function index(Request $req) {
        return $this->add_edit_and_listing($req);
    }
    public function add(Request $req) {
        $data = $req->all();
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'title' => 'required',
            'slug' => 'required|unique:pages',
            'template_id' => 'required',
            'content' => 'required',
            'featured_image' => 'required||file|max:1000|mimes:'.__get_image_extensions('string'),
        ]);
        $data['user_id'] = __c_user()->id;
        $data['post_status'] = 'drafted';
        if ($data['_status'] == 'Publish') {
            $data['post_status'] = 'published';
        }
        if ($validated->fails()) {
            $response['errors'] = $validated->getMessageBag()->toArray();
            $response['status'] = 'fail';
            return $response;
        }
        $image = $req->file('featured_image');
        $input['imagename'] = 'img-'.uniqid().time().'.'.$image->extension();
        $path = public_path('/assets/images');
        if(!File::exists($path)){
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        $img = Image::make($image->path());
        $img->save($path.'/'.$input['imagename'], 50);
        $data['featured_image'] = $input['imagename'];
        if(Page::create($data)) {
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (__c_user()->is_super_admin != 1) {
            if (Page::where(['id' => $id, 'user_id' => __c_user()->id])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $page = Page::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            // print_r($data); die;
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'title' => 'required',
                'slug' => 'required|unique:pages',
                'template_id' => 'required',
                'content' => 'required',
                'featured_image' => 'required|file|max:1000|mimes:'.__get_image_extensions('string'),
            ];
            if ($data['_featured_image']  == $page->featured_image)
                $vArgs['featured_image'] = 'file|max:1000|mimes:'.__get_image_extensions('string');
            
            if ($data['slug'] == $page->slug)
                unset($vArgs['slug']);

            $validated = Validator::make($data, $vArgs);
            $data['post_status'] = 'drafted';
            if ($data['_status'] == 'Publish' || $data['_status'] == 'Update') {
                $data['post_status'] = 'published';
            }
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }

            $data['featured_image'] = $data['_featured_image'];
            if ($req->file('featured_image')) {
                File::delete('public/assets/images/'.$data['_featured_image']);
                $image = $req->file('featured_image');
                $input['imagename'] = 'img-'.uniqid().time().'.'.$image->extension();
                $path = public_path('/assets/images');
                if(!File::exists($path)){
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $img = Image::make($image->path());
                $img->save($path.'/'.$input['imagename'], 50);
                $data['featured_image'] = $input['imagename'];
            }

            if($page->update($data)) {
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                $response = ['status' => 'success', 'item' => $page];
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
                if (Page::where(['id' => $id, 'user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Page::whereIn('id', $req->input('action_ids'))->where(['user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }
        if ($id) {
            if (Page::where(['id' => $id])->where('post_status', '!=', 'trashed')->count() > 0) {
               Page::where(['id' => $id])->where('post_status', '!=', 'trashed')->update(['post_status' => 'trashed']);
            }

            else if (Page::where(['id' => $id, 'post_status' => 'trashed'])->count() > 0) {
                $fImg = Page::select('featured_image')->where(['id' => $id])->first()->toArray()['featured_image'];
                File::delete('public/assets/images/'.$fImg);
                Page::where(['id' => $id, 'post_status' => 'trashed'])->delete();
            }
            return back()->with('msg', 'Delete successfully');
        }
        else {
            if ($req->input('rec_action') == 'trash') {
                if (Page::whereIn('id', $req->input('action_ids'))->where('post_status', '!=', 'trashed')->count() > 0) {
                    Page::whereIn('id', $req->input('action_ids'))->where('post_status', '!=', 'trashed')->update(['post_status' => 'trashed']);
                }
            }
            if ($req->input('rec_action') == 'delete') {
                if (Page::whereIn('id', $req->input('action_ids'))->where(['post_status' => 'trashed'])->count() > 0) {
                    $fImgs = Page::select('featured_image')->whereIn('id', $req->input('action_ids'))->get()->toArray();
                    foreach ($fImgs as $key => $fImg) {
                        File::delete('public/assets/images/'.$fImg['featured_image']);
                    }
                    
                    Page::whereIn('id', $req->input('action_ids'))->where(['post_status' => 'trashed'])->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    public function restore($id = null, Request $req) {
        if (__c_user()->is_super_admin != 1) {
            if ($id) {
                if (Page::where(['id' => $id, 'user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Page::whereIn('id', $req->input('action_ids'))->where(['user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }

        if ($id) {
            if (Page::where(['id' => $id, 'post_status' => 'trashed'])->count() > 0) {
                Page::where(['id' => $id, 'post_status' => 'trashed'])->update(['post_status' => 'published']);
                return back()->with('msg', 'Restored successfully');
            }
        }
        else {
            if ($req->input('rec_action') == 'restore') {
                if (Page::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->count() > 0) {

                    Page::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->update(['post_status' => 'published']);
                    return back()->with('msg', 'Restored successfully');
                }
            }
        }
    }
    

    
}
