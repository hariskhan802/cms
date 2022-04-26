<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use Illuminate\Support\Facades\Validator;
use File;
use Image;

class TermController extends Controller
{
    private function add_edit_and_listing($req) {
        $term1 = Term::query();
        $term2 = Term::query();
        $term3 = Term::query();
        $name = 'term';
        $totalRecords = $term1->count();
        if ($req->input('search')) {
            $term2->where('terms.name', 'like', "%{$req->input('search')}%");
        }
        if (c_user()->is_super_admin != 1) {
            $term2->where('terms.user_id', '=', c_user()->ID);
            $term3->where('terms.user_id', '=', c_user()->ID);
        }
        // if ($req->input('status') == '') {
        //     $term2->where(['status' => 'published']);
        // }
        // if ($req->input('search') == '') {
        //     $term2->where(['parent_id' => '0']);
        // }
        // $term3->where(['parent_id' => '0']);
        // $term2 = ;
        return view('Admin.Term.index', ['name' => $name, 'totalRecords' => $totalRecords, 'data' => $term2->select(['terms.term_id', 'terms.name',  'terms.slug'])->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->orderBy('terms.term_id', 'DESC')->paginate(10), 'terms' => $term3->orderBy('terms.term_id', 'DESC')->get()]);
    }

    public function index(Request $req) {
        return $this->add_edit_and_listing($req);        
    }
    public function add(Request $req) {
        $data = $req->all();
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'title' => 'required',
            'slug' => 'required|unique:terms',
            'description' => 'required',
            'featured_image' => 'required||file|max:1000|mimes:'.get_image_extensions('string'),
        ]);
        $data['user_id'] = c_user()->ID;
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
        if(Term::create($data)) {
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (c_user()->is_super_admin != 1) {
            if (Term::where(['id' => $id, 'user_id' => c_user()->ID])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $term = Term::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'title' => 'required',
                'slug' => 'required|unique:terms',
                'parent_id' => 'required',
                'description' => 'required',
                'featured_image' => 'required|file|max:1000|mimes:'.get_image_extensions('string'),
            ];
            $data['menu_order'] = 0;
            if ($data['_featured_image']  == $term->featured_image)
                $vArgs['featured_image'] = 'file|max:1000|mimes:'.get_image_extensions('string');
            
            if ($data['slug'] == $term->slug)
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

            if($term->update($data)) {
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                $response = ['status' => 'success', 'item' => $term];
                return $response;
            }
            else {
                return $this->add_edit_and_listing($req);
            }
        }

    }
    public function delete($id = null, Request $req) {
        if (c_user()->is_super_admin != 1) {
            if ($id) {
                if (Term::where(['id' => $id, 'user_id' => c_user()->ID])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Term::whereIn('id', $req->input('action_ids'))->where(['user_id' => c_user()->ID])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }
        if ($id) {
            if (Term::where(['id' => $id])->count() > 0) {
                Term::where(['parent_id' => $id])->update(['parent_id' => 0]);
                $fImg = Term::select('featured_image')->where(['id' => $id])->first()->toArray()['featured_image'];
                File::delete('public/assets/images/'.$fImg);
                Term::where(['id' => $id])->delete();
            }

            return back()->with('msg', 'Delete successfully');
        }
        else {
            
            if ($req->input('rec_action') == 'delete') {
                if (Term::whereIn('id', $req->input('action_ids'))->count() > 0) {
                    Term::whereIn('parent_id', $req->input('action_ids'))->update(['parent_id' => 0]);
                    $fImgs = Term::select('featured_image')->whereIn('id', $req->input('action_ids'))->get()->toArray();
                    foreach ($fImgs as $key => $fImg) {
                        File::delete('public/assets/images/'.$fImg['featured_image']);
                    }
                    Term::whereIn('id', $req->input('action_ids'))->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    

}
