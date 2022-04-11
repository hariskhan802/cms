<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use File;
use Image;

class CategoryController extends Controller
{
    private function add_edit_and_listing($req) {
        $category1 = Category::query();
        $category2 = Category::query();
        $category3 = Category::query();
        $name = 'category';
        $totalRecords = $category1->count();
        if ($req->input('search')) {
            $category2->where('categories.title', 'like', "%{$req->input('search')}%");
        }
        if (__c_user()->is_super_admin != 1) {
            $category2->where('categories.user_id', '=', __c_user()->id);
            $category3->where('categories.user_id', '=', __c_user()->id);
        }
        if ($req->input('status') == '') {
            $category2->where(['status' => 'published']);
        }
        if ($req->input('search') == '') {
            $category2->where(['parent_id' => '0']);
        }
        $category3->where(['parent_id' => '0']);
        // $category2 = ;
        return view('Admin.Category.index', ['name' => $name, 'totalRecords' => $totalRecords, 'data' => $category2->select(['categories.id', 'categories.title', 'categories.description', 'categories.featured_image', 'categories.created_at'])->orderBy('categories.id', 'DESC')->paginate(10), 'categories' => $category3->orderBy('categories.id', 'DESC')->get()]);
    }

    public function index(Request $req) {
        return $this->add_edit_and_listing($req);        
    }
    public function add(Request $req) {
        $data = $req->all();
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'title' => 'required',
            'slug' => 'required|unique:categories',
            'description' => 'required',
            'featured_image' => 'required||file|max:1000|mimes:'.__get_image_extensions('string'),
        ]);
        $data['user_id'] = __c_user()->id;
        if ($data['_status'] == 'Publish') {
            $data['status'] = 'published';
        }
        if ($validated->fails()) {
            $response['errors'] = $validated->getMessageBag()->toArray();
            $response['status'] = 'fail';
            return $response;
        }
        $data['menu_order'] = 0;
        $image = $req->file('featured_image');
        $input['imagename'] = 'img-'.uniqid().time().'.'.$image->extension();
        $path = public_path('/assets/images');
        if(!File::exists($path)){
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        $img = Image::make($image->path());
        
        $img->save($path.'/'.$input['imagename'], 50);
        $data['featured_image'] = $input['imagename'];
        if(Category::create($data)) {
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (__c_user()->is_super_admin != 1) {
            if (Category::where(['id' => $id, 'user_id' => __c_user()->id])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $category = Category::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'title' => 'required',
                'slug' => 'required|unique:categories',
                'parent_id' => 'required',
                'description' => 'required',
                'featured_image' => 'required|file|max:1000|mimes:'.__get_image_extensions('string'),
            ];
            $data['menu_order'] = 0;
            if ($data['_featured_image']  == $category->featured_image)
                $vArgs['featured_image'] = 'file|max:1000|mimes:'.__get_image_extensions('string');
            
            if ($data['slug'] == $category->slug)
                unset($vArgs['slug']);

            $validated = Validator::make($data, $vArgs);
            if ($data['_status'] == 'Publish' || $data['_status'] == 'Update') {
                $data['status'] = 'published';
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

            if($category->update($data)) {
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                $response = ['status' => 'success', 'item' => $category];
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
                if (Category::where(['id' => $id, 'user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Category::whereIn('id', $req->input('action_ids'))->where(['user_id' => __c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }
        if ($id) {
            if (Category::where(['id' => $id])->count() > 0) {
                Category::where(['parent_id' => $id])->update(['parent_id' => 0]);
                $fImg = Category::select('featured_image')->where(['id' => $id])->first()->toArray()['featured_image'];
                File::delete('public/assets/images/'.$fImg);
                Category::where(['id' => $id])->delete();
            }

            return back()->with('msg', 'Delete successfully');
        }
        else {
            
            if ($req->input('rec_action') == 'delete') {
                if (Category::whereIn('id', $req->input('action_ids'))->count() > 0) {
                    Category::whereIn('parent_id', $req->input('action_ids'))->update(['parent_id' => 0]);
                    $fImgs = Category::select('featured_image')->whereIn('id', $req->input('action_ids'))->get()->toArray();
                    foreach ($fImgs as $key => $fImg) {
                        File::delete('public/assets/images/'.$fImg['featured_image']);
                    }
                    Category::whereIn('id', $req->input('action_ids'))->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    

}
