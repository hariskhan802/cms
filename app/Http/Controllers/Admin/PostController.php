<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use File;
use Image;

class PostController extends Controller
{
    private $currentPostType;
    public function __construct(Request $req) {
        $this->middleware(function ($request, $next) use ($req) {
            $this->currentPostType = get_current_post_type($req->input('post_type'));
            if (!$this->currentPostType || !isset($this->currentPostType['post_type']) || empty($this->currentPostType['post_type']))
                return redirect(route('dashboard'))->with('errormsg', 'Invalid Post type');
            if (!check_own_record_or_has_permission(Post::class, $req))            
                return redirect(route('dashboard'))->with('errormsg', 'Permission Denied');

            return $next($request);
        });
        
    }
    
    private function add_edit_and_listing($req) {
        $post1 = Post::query();
        $post2 = Post::query();
        
        $name = 'post';
        if (c_user()->is_super_admin != 1) {
            $post1->where('posts.user_id', '=', c_user()->id);
            $post2->where('posts.user_id', '=', c_user()->id);
        }
        $totalRecords = $post1->where('post_status', '!=', 'trashed')->where(['post_type' => $this->currentPostType['post_type']])->count();
        $post2->where(['post_type' => $this->currentPostType['post_type']]);
        if ($req->input('search')) {
            $post2->where('posts.title', 'like', "%{$req->input('search')}%");
        }
        
        if ($req->input('status') == '') {
            $post2->where(['post_status' => 'published'])->orWhere(['post_status' => 'drafted']);
        }
        else if ($req->input('status') == 'published') {
            $post2->where(['post_status' => 'published']);
        }
        else if ($req->input('status') == 'drafts') {
            $post2->where(['post_status' => 'drafted']);
        }   
        else if ($req->input('status') == 'trash') {
            $post2->where(['post_status' => 'trashed']);
        }
        return view('Admin.Post.index', ['postType' => $this->currentPostType['post_type'], 'currentPostType' => $this->currentPostType, 'totalRecords' => $totalRecords, 'data' => $post2->select(['posts.id', 'posts.title', 'posts.slug', 'posts.featured_image', 'posts.created_at', 'posts.updated_at'])->orderBy('posts.id', 'DESC')->paginate(10)]);
    }
    public function index(Request $req) {
        
        return $this->add_edit_and_listing($req);
    }
    public function add(Request $req) {
        
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $validated = Validator::make($data, [
                'title' => 'required',
                'slug' => 'required|unique:posts',
                'content' => 'required',
                'featured_image' => 'required||file|max:1000|mimes:'.get_image_extensions('string'),
            ]);
            $data['user_id'] = c_user()->id;

            $data['post_status'] = 'drafted';
            if ($data['_status'] == 'Publish') {
                $data['post_status'] = 'published';
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
            if($pID = Post::create($data)->id) {
                foreach ($data['cats'] as $key => $cat) {
                    \App\Models\PostCategoryRelation::create(['post_id' => $pID, 'cat_id' => $cat]);
                }
                
                $response['status'] = 'success';
                $response['message'] = 'You have added successfully';
            }
            return $response;
        }
        else {
            return view('Admin.Post.add-edit', ['postType' => $this->currentPostType['post_type'], 'currentPostType' => $this->currentPostType]);
        }
    }
    public function edit($id, Request $req) {
        /* if (!check_own_record_or_has_permission(Post::class, $req)) 
            return back()->with('errormsg', 'Permission Denied'); */
        
        $post = Post::findOrfail($id);
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'title' => 'required',
                'slug' => 'required|unique:posts',
                'content' => 'required',
                'featured_image' => 'required|file|max:1000|mimes:'.get_image_extensions('string'),
            ];
            if ($data['_featured_image'] == $post->featured_image)
                $vArgs['featured_image'] = 'file|max:1000|mimes:'.get_image_extensions('string');
            
            if ($data['slug'] == $post->slug)
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
            $data['menu_order'] = 0;
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

            if($post->update($data)) {
                \App\Models\PostCategoryRelation::where(['post_id' => $id])->delete();
                foreach ($data['cats'] as $key => $cat) {
                    \App\Models\PostCategoryRelation::create(['post_id' => $id, 'cat_id' => $cat]);
                }
                $response['status'] = 'success';
                $response['message'] = 'You have updated successfully';
            }
            return $response;
        }
        else {
            if ($req->ajax()) {
                // dd($post);
                // die('test');
                $response = ['status' => 'success', 'item' => $post];
                return $response;
            }
            else {
                return view('Admin.Post.add-edit', ['postType' => $this->currentPostType['post_type'], 'currentPostType' => $this->currentPostType,]);
            }
        }

    }
    public function delete($id = null, Request $req) {
        /* if (!check_own_record_or_has_permission(Post::class, $req)) 
            return back()->with('errormsg', 'Permission Denied'); */
        
        if ($id) {
            if (Post::where(['id' => $id])->where('post_status', '!=', 'trashed')->count() > 0) {
               Post::where(['id' => $id])->where('post_status', '!=', 'trashed')->update(['post_status' => 'trashed']);
            }

            else if (Post::where(['id' => $id, 'post_status' => 'trashed'])->count() > 0) {
                $fImg = Post::select('featured_image')->where(['id' => $id])->first()->toArray()['featured_image'];
                File::delete('public/assets/images/'.$fImg);
                Post::where(['id' => $id, 'post_status' => 'trashed'])->delete();
            }
            return back()->with('msg', 'Delete successfully');
        }
        else {
            if ($req->input('rec_action') == 'trash') {
                if (Post::whereIn('id', $req->input('action_ids'))->where('post_status', '!=', 'trashed')->count() > 0) {
                    Post::whereIn('id', $req->input('action_ids'))->where('post_status', '!=', 'trashed')->update(['post_status' => 'trashed']);
                }
            }
            if ($req->input('rec_action') == 'delete') {
                if (Post::whereIn('id', $req->input('action_ids'))->where(['post_status' => 'trashed'])->count() > 0) {
                    $fImgs = Post::select('featured_image')->whereIn('id', $req->input('action_ids'))->get()->toArray();
                    foreach ($fImgs as $key => $fImg) {
                        File::delete('public/assets/images/'.$fImg['featured_image']);
                    }
                    Post::whereIn('id', $req->input('action_ids'))->where(['post_status' => 'trashed'])->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    public function restore($id = null, Request $req) {
        /* if (!check_own_record_or_has_permission(Post::class, $req)) 
            return back()->with('errormsg', 'Permission Denied'); */

        if ($id) {
            if (Post::where(['id' => $id, 'post_status' => 'trashed'])->count() > 0) {
                Post::where(['id' => $id, 'post_status' => 'trashed'])->update(['post_status' => 'published']);
                return back()->with('msg', 'Restored successfully');
            }
        }
        else {
            if ($req->input('rec_action') == 'restore') {
                if (Post::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->count() > 0) {

                    Post::whereIn('id', $req->input('action_ids'))->where('post_status', 'trashed')->update(['post_status' => 'published']);
                    return back()->with('msg', 'Restored successfully');
                }
            }
        }
    }
    /*public function posts_ajax(Request $req) {
        
        // return data_table($req->all(), Post::class, ['id', 'title', 'template_id AS template', 'featured_image', 'created_at AS date'], )['backend'];
    
        return data_table(['inputs' => $req->all(), 'table' => Post::class, 'columns' => ['id', 'title', 'template_id AS template', 'featured_image', 'created_at AS date'], 'tColumns' => ['ID', 'Title', 'Template', 'Featured Image', 'Date']])['backend'];
    }*/

}
