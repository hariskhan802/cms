<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Validator;

class CommentController extends Controller
{
    private function add_edit_and_listing($req) {
        $comment1 = Comment::query();
        $comment2 = Comment::query();
        $name = 'comment';
        $totalRecords = $comment1->where('comments.status', '!=', 'trashed')->count();
        if ($req->input('search')) {
            $comment2->where('comments.comment', 'like', "%{$req->input('search')}%")->orWhere('users.name', 'like', "%{$req->input('search')}%");
        }
        if ($req->input('status') != 'trash') {
            $comment2->where('comments.status', '!=', 'trashed');
        }
        if (c_user()->is_super_admin != 1) {
            $comment2->where('comments.user_id', '=', c_user()->id);
        }
        if ($req->input('status') == 'mine') {
            $comment2->where(['comments.user_id' => c_user()->id]);
        }
        if ($req->input('status') == 'approved') {
            $comment2->where(['comments.status' => 'approved']);
        }
        if ($req->input('status') == 'pending') {
            $comment2->where(['comments.status' => 'pending']);
        }
        else if ($req->input('status') == 'spam') {
            $comment2->where(['comments.status' => 'spam']);
        }   
        else if ($req->input('status') == 'trash') {
            $comment2->where(['comments.status' => 'trashed']);
        }
        
        
        return view('Admin.Comment.index', ['name' => $name, 'totalRecords' => $totalRecords, 'data' => $comment2->select(['comments.id', 'comments.comment', 'comments.user_id', 'comments.post_id', 'comments.status', 'comments.created_at', 'users.name', 'posts.title', 'comments.updated_at'])->leftJoin('users', 'users.id', '=', 'comments.user_id')->leftJoin('posts', 'posts.id', '=', 'comments.post_id')->orderBy('comments.id', 'DESC')->paginate(10)]);
    }
    public function index(Request $req) {
        return $this->add_edit_and_listing($req);        
    }
    public function add(Request $req) {
        $data = $req->all();
        // print_r($data); die;
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'title' => 'required',
            'slug' => 'required|unique:comments',
            'content' => 'required',
            'featured_image' => 'required||file|max:1000|mimes:'.get_image_extensions('string'),
        ]);
        $data['user_id'] = c_user()->id;

        
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
        if($pID = Comment::create($data)->id) {
            foreach ($data['cats'] as $key => $cat) {
                \App\Models\PostCategoryRelation::create(['post_id' => $pID, 'cat_id' => $cat]);
            }
            
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (c_user()->is_super_admin != 1) {
            if (Comment::where(['id' => $id, 'user_id' => c_user()->id])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $comment = Comment::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'comment' => 'required',
                'status' => 'required',
            ];
            

            $validated = Validator::make($data, $vArgs);
            
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }
            

            if($comment->update($data)) {
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                $response = ['status' => 'success', 'item' => $comment];
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
                if (Comment::where(['id' => $id, 'user_id' => c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Comment::whereIn('id', $req->input('action_ids'))->where(['user_id' => c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }
        if ($id) {
            if (Comment::where(['id' => $id])->where('status', '!=', 'trashed')->count() > 0) {
               Comment::where(['id' => $id])->where('status', '!=', 'trashed')->update(['status' => 'trashed']);
            }

            else if (Comment::where(['id' => $id, 'status' => 'trashed'])->count() > 0) {
                $fImg = Comment::select('featured_image')->where(['id' => $id])->first()->toArray()['featured_image'];
                File::delete('public/assets/images/'.$fImg);
                Comment::where(['id' => $id, 'status' => 'trashed'])->delete();
            }
            return back()->with('msg', 'Delete successfully');
        }
        else {
            if ($req->input('rec_action') == 'trash') {
                if (Comment::whereIn('id', $req->input('action_ids'))->where('status', '!=', 'trashed')->count() > 0) {
                    Comment::whereIn('id', $req->input('action_ids'))->where('status', '!=', 'trashed')->update(['status' => 'trashed']);
                }
            }
            if ($req->input('rec_action') == 'delete') {
                if (Comment::whereIn('id', $req->input('action_ids'))->where(['status' => 'trashed'])->count() > 0) {
                    $fImgs = Comment::select('featured_image')->whereIn('id', $req->input('action_ids'))->get()->toArray();
                    foreach ($fImgs as $key => $fImg) {
                        File::delete('public/assets/images/'.$fImg['featured_image']);
                    }
                    Comment::whereIn('id', $req->input('action_ids'))->where(['status' => 'trashed'])->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    public function restore($id = null, Request $req) {
        if (c_user()->is_super_admin != 1) {
            if ($id) {
                if (Comment::where(['id' => $id, 'user_id' => c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Comment::whereIn('id', $req->input('action_ids'))->where(['user_id' => c_user()->id])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }

        if ($id) {
            if (Comment::where(['id' => $id, 'status' => 'trashed'])->count() > 0) {
                Comment::where(['id' => $id, 'status' => 'trashed'])->update(['status' => 'pending']);
                return back()->with('msg', 'Restored successfully');
            }
        }
        else {
            if ($req->input('rec_action') == 'restore') {
                if (Comment::whereIn('id', $req->input('action_ids'))->where('status', 'trashed')->count() > 0) {

                    Comment::whereIn('id', $req->input('action_ids'))->where('status', 'trashed')->update(['status' => 'pending']);
                    return back()->with('msg', 'Restored successfully');
                }
            }
        }
    }
    
}
