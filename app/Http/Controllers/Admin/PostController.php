<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use File;
use Image;
use Illuminate\Support\Str;
use App\Models\{
    Post,
    TermRelationship,
    Template,
};

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
        if (array_value(c_user(), 'is_super_admin') != 1) {
            $post1->where('posts.post_author', '=', array_value(c_user(), 'ID'));
            $post2->where('posts.post_author', '=', array_value(c_user(), 'ID'));
        }
        if ($req->input('status') == '') {
            // $post1/* ->where([]) */->orWhere(['post_status' => 'publish', 'post_status' => 'draft']);
            // $post2/* ->where([]) */->orWhere(['post_status' => 'publish', 'post_status' => 'draft']);
            $post1 = $post1->where(function($q) {
                $q->where('post_status', 'publish')->orWhere('post_status', 'draft');
            });
            $post2 = $post2->where(function($q) {
                $q->where('post_status', 'publish')->orWhere('post_status', 'draft');
            });
        }
        else {
            $post1->where(['post_status' => $req->input('status')]);
            $post2->where(['post_status' => $req->input('status')]);
        }
        // dd($post1->toSql());
        $totalRecords = $post1->where(['post_type' => $this->currentPostType['post_type']])->count();
        // $post2;
        if ($req->input('search')) {
            $post2->where('posts.post_title', 'like', "%{$req->input('search')}%");
        }
        return view('Admin.Post.index', ['postType' => $this->currentPostType['post_type'], 
        'currentPostType' => $this->currentPostType, 
        'totalRecords' => $totalRecords,
        'data' => $post2->select(['posts.ID', 'posts.post_title', 'posts.post_name', 'posts.post_date', 'posts.post_modified', 'users.display_name'])->join('users', 'posts.post_author', '=', 'users.id')->where(['post_type' => $this->currentPostType['post_type']])->orderBy('posts.id', 'DESC')->paginate(10)]);
    }
    public function index(Request $req) {
        
        return $this->add_edit_and_listing($req);
    }
    public function add(Request $req) {
        
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $validated = Validator::make($data, [
                'post_title' => 'required',
                'featured_image' => 'file|max:1000|mimes:'.get_image_extensions('string'),
            ]);
            $data['post_author'] = array_value(c_user(), 'ID');
            
            $data['post_status'] = 'draft';
            if (array_value($data, '_status') == 'Publish') {
                $data['post_status'] = 'publish';
            }
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }
            $data['post_content_filtered'] = '';
            $data['post_password'] = '';
            $data['post_mime_type'] = '';
            $data['pinged'] = '';
            $data['to_ping'] = '';
            $data['guid'] = '';
            $data['slug'] = array_value($data, 'slug') != '' ? array_value($data, 'slug') : Str::slug(array_value($data, 'post_title'), '-');
            $data['post_name'] = array_value($data, 'slug');
            $data['post_excerpt'] = array_value($data, 'post_excerpt') ? array_value($data, 'post_excerpt')  : '';
            $data['post_content'] = array_value($data, 'post_content') ? array_value($data, 'post_content')  : '';
            // $data['post_excerpt'] = array_value($data, 'post_excerpt') ? array_value($data, 'post_excerpt')  : '';
            // $data['post_excerpt'] = array_value($data, 'post_excerpt') ? array_value($data, 'post_excerpt')  : '';
            $templateID = array_value($data, 'template_id');
            unset($data['slug']);
            // print_r($data); die;
            if(Post::select('post_name')->where('post_name', $data['post_name'])->count() > 0){
                $response['errors'] = ['slug' => 'Slug is already taken!'];
                $response['status'] = 'fail';
                return $response;
            }
            $data['menu_order'] = 0;
            $featuredImage = '';
            if ($req->file('featured_image')) {
                $image = $req->file('featured_image');
                $featuredImage = 'img-'.uniqid().time().'.'.$image->extension();
                $path = public_path('/assets/images');
                if(!File::exists($path)){
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $img = Image::make($image->path());
                $img->save($path.'/'.$featuredImage, 50);
            }
            
            if($pID = Post::create($data)->ID) {
                
                if ($featuredImage != '') 
                    update_post_meta($pID, '__featured_image', $featuredImage);
                
                if ($templateID != '')
                    update_post_meta($id, '__template_id', $templateID);

                Post::find($pID)->update(['guid' => url('?'.$data['post_type'].'='.$pID)]);
                if (is_array(@$data['cats'])) {
                    foreach ($data['cats'] as $key => $cat) {
                        TermRelationship::create(['object_id' => $pID, 'term_taxonomy_id' => $cat]);
                    }
                }
                $response['status'] = 'success';
                $response['message'] = 'You have added successfully';
            }
            return $response;
        }
        else {
            return view('Admin.Post.add-edit', [
                'postType' => $this->currentPostType['post_type'], 
                'currentPostType' => $this->currentPostType,
                'templates' => Template::orderBy('id', 'DESC')->get(),
            ]);
        }
    }
    public function edit($id, Request $req) {
        /* if (!check_own_record_or_has_permission(Post::class, $req)) 
            return back()->with('errormsg', 'Permission Denied'); */
        
        $post = Post::findOrfail($id);
        
        $post->slug = $post->post_name;
        $post->featured_image = get_post_meta($id, '__featured_image', true);
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'post_title' => 'required',
            ];
            $data['slug'] = array_value($data, 'slug') != '' ? array_value($data, 'slug') : Str::slug(array_value($data, 'post_title'), '-');
            if (array_value($data, '_featured_image') == $post->featured_image)
                $vArgs['featured_image'] = 'file|max:1000|mimes:'.get_image_extensions('string');
            
            if (Post::select(['post_name'])->where(['post_name' => array_value($data, 'slug')])->count() > 0) {
                if ($post->post_name != array_value($data, 'slug')) {
                    $response['errors'] = ['slug' => 'Slug is already taken!'];
                    $response['status'] = 'fail';
                    return $response;
                }
            }
            $data['post_name'] = array_value($data, 'slug');
            $data['post_excerpt'] = array_value($data, 'post_excerpt') ? array_value($data, 'post_excerpt')  : '';
            $data['post_content'] = array_value($data, 'post_content') ? array_value($data, 'post_content')  : '';
            
            $validated = Validator::make($data, $vArgs);
            $data['post_status'] = 'draft';
            if (array_value($data, '_status') == 'Publish' || array_value($data, '_status') == 'Update') {
                $data['post_status'] = 'publish';
            }
            if ($validated->fails()) {
                $response['errors'] = $validated->getMessageBag()->toArray();
                $response['status'] = 'fail';
                return $response;
            }
            $data['menu_order'] = 0;
            $featuredImage = array_value($data, '_featured_image');
            if ($req->file('featured_image')) {
                if(File::exists('public/assets/images/'.array_value($data, '_featured_image')))
                    File::delete('public/assets/images/'.array_value($data, '_featured_image'));

                $image = $req->file('featured_image');
                $featuredImage = 'img-'.uniqid().time().'.'.$image->extension();
                // print_r($featuredImage); die;
                $path = public_path('/assets/images');
                if(!File::exists($path)){
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $img = Image::make($image->path());
                $img->save($path.'/'.$featuredImage, 50);
                $featuredImage = $featuredImage;
                
            }
            $cats = array_value($data, 'cats');
            $data['ID'] = $id;
            $templateID = array_value($data, 'template_id');

            unset($post->slug, $post->featured_image, $data['slug'], $data['_token'], $data['_status'], $data['_featured_image'], $data['featured_image'], $data['submit'], $data['cats']);
            // print_r($id); 
            // print_r(Post::where(['ID' => $id])->get()->toArray() );
            // print_r(Post::where(['ID' => $id])->update($data)); 
            // print_r($data); 
            // die;
            if($post->update($data)) {
                if ($featuredImage != '') 
                    update_post_meta($id, '__featured_image', $featuredImage);

                if ($templateID != '')
                    update_post_meta($id, '__template_id', $templateID);

                TermRelationship::where(['object_id' => $id])->delete();
                if (is_array($cats)) {
                    foreach ($cats as $key => $cat) {
                        TermRelationship::create(['object_id' => $id, 'term_taxonomy_id' => $cat]);
                    }
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
                return view('Admin.Post.add-edit', [
                    'postType' => $this->currentPostType['post_type'], 
                    'currentPostType' => $this->currentPostType,
                    'templates' => Template::orderBy('id', 'DESC')->get(),
                ]);
            }
        }

    }
    public function delete($id = null, Request $req) {
        /* if (!check_own_record_or_has_permission(Post::class, $req)) 
            return back()->with('errormsg', 'Permission Denied'); */
        
        if ($id) {
            if (Post::where(['id' => $id])->where('post_status', '!=', 'trash')->count() > 0) {
               Post::where(['id' => $id])->where('post_status', '!=', 'trash')->update(['post_status' => 'trash']);
            }

            else if (Post::where(['id' => $id, 'post_status' => 'trash'])->count() > 0) {
                $fImg = get_post_meta($id, '__featured_image', true);
                File::delete('public/assets/images/'.$fImg);
                Post::where(['id' => $id, 'post_status' => 'trash'])->delete();
            }
            return back()->with('msg', 'Delete successfully');
        }
        else {
            if ($req->input('rec_action') == 'trash') {
                if (Post::whereIn('id', $req->input('action_ids'))->where('post_status', '!=', 'trash')->count() > 0) {
                    Post::whereIn('id', $req->input('action_ids'))->where('post_status', '!=', 'trash')->update(['post_status' => 'trash']);
                }
            }
            if ($req->input('rec_action') == 'delete') {
                if (Post::whereIn('id', $req->input('action_ids'))->where(['post_status' => 'trash'])->count() > 0) {
                    
                    foreach($req->input('action_ids') as $fid) {
                        $fImg = get_post_meta($fid, '__featured_image', true);
                        File::delete('public/assets/images/'.$fImg);
                    }
                    Post::whereIn('id', $req->input('action_ids'))->where(['post_status' => 'trash'])->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    public function restore($id = null, Request $req) {
        /* if (!check_own_record_or_has_permission(Post::class, $req)) 
            return back()->with('errormsg', 'Permission Denied'); */

        if ($id) {
            if (Post::where(['id' => $id, 'post_status' => 'trash'])->count() > 0) {
                Post::where(['id' => $id, 'post_status' => 'trash'])->update(['post_status' => 'draft']);
                return back()->with('msg', 'Restored successfully');
            }
        }
        else {
            if ($req->input('rec_action') == 'restore') {
                if (Post::whereIn('id', $req->input('action_ids'))->where('post_status', 'trash')->count() > 0) {

                    Post::whereIn('id', $req->input('action_ids'))->where('post_status', 'trash')->update(['post_status' => 'draft']);
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
