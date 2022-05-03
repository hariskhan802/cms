<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use Illuminate\Support\Facades\Validator;
use File;
use Image;
use App\Models\TermTaxonomy;

class TermController extends Controller
{
    private $currentPostType, $currentTaxonomy;
    public function __construct(Request $req) {
        $this->middleware(function ($request, $next) use ($req) {
            $this->currentPostType = get_current_post_type($req->input('post_type'));
            $this->currentTaxonomy = get_current_taxonomy($req->input('taxonomy'));
            if ((!$this->currentPostType || !isset($this->currentPostType['post_type']) && !$this->currentTaxonomy || !isset($this->currentTaxonomy['taxonomy'])) || empty($this->currentPostType['post_type']))
                return redirect(route('dashboard'))->with('errormsg', 'Invalid Post type or Taxonomy');
            if (!check_own_record_or_has_permission(Post::class, $req))            
                return redirect(route('dashboard'))->with('errormsg', 'Permission Denied');

            return $next($request);
        });
        
    }

    private function add_edit_and_listing($req) {
        $term1 = Term::query();
        $term2 = Term::query();
        $term3 = Term::query();

        $term3->join('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id');
        $name = array_value($this->currentTaxonomy, 'taxonomy');
        $totalRecords = $term1->join('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->where(['term_taxonomy.taxonomy' => $this->currentTaxonomy['taxonomy']])->count();
        if ($req->input('search')) {
            $term2->where('terms.name', 'like', "%{$req->input('search')}%");
        }
        
        // if ($req->input('status') == '') {
        //     $term2->where(['status' => 'published']);
        // }
        // if ($req->input('search') == '') {
        //     $term2->where(['parent_id' => '0']);
        // }
        // $term3->where(['parent_id' => '0']);
        // $term2 = ;
        return view('Admin.Term.index', ['name' => $name,'currentPostType' => $this->currentPostType, 
        'currentTaxonomy' => $this->currentTaxonomy, 'totalRecords' => $totalRecords, 
        'data' => $term2->select(['terms.term_id', 'terms.name',  'terms.slug', 'term_taxonomy.taxonomy',
        'term_taxonomy.description', 'term_taxonomy.parent', 'term_taxonomy.count'])
        ->join('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')
        ->where(['term_taxonomy.taxonomy' => $this->currentTaxonomy['taxonomy']])
        ->orderBy('terms.term_id', 'DESC')
        ->paginate(10), 'terms' => $term3->where(['term_taxonomy.taxonomy' => $this->currentTaxonomy['taxonomy']])->orderBy('terms.term_id', 'DESC')->get()]);
    }

    public function index(Request $req) {
        return $this->add_edit_and_listing($req);        
    }
    public function add(Request $req) {
        $data = $req->all();
        $response = ['status' => [], 'errors' => []];
        $validated = Validator::make($data, [
            'name' => 'required',
            'slug' => 'unique:terms',
            'featured_image' => 'file|max:1000|mimes:'.get_image_extensions('string'),
        ]);
        $data['user_id'] = array_value(c_user(), 'ID');
        if ($data['_status'] == 'Publish') {
            $data['status'] = 'published';
        }
        if ($validated->fails()) {
            $response['errors'] = $validated->getMessageBag()->toArray();
            $response['status'] = 'fail';
            return $response;
        }
        $data['menu_order'] = 0;
        $featuredImage = '';
        if($req->file('featured_image')) {

            $image = $req->file('featured_image');
            $featuredImage = 'img-'.uniqid().time().'.'.$image->extension();
            $path = public_path('/assets/images');
            if(!File::exists($path)){
                File::makeDirectory($path, $mode = 0777, true, true);
            }
            $img = Image::make($image->path());
            
            $img->save($path.'/'.$featuredImage, 50);
        }
        
        if($termID = Term::create($data)->term_id) {
            TermTaxonomy::create([
                'term_id' => $termID,
                'taxonomy' => $this->currentTaxonomy['taxonomy'],
                'description' => array_value($data, 'description') ? array_value($data, 'description') : '',
                'parent' => $data['parent_id'],
                // 'count' => $termID,

            ]);
            $response['status'] = 'success';
            $response['message'] = 'You have added successfully';
        }
        return $response;
    }
    public function edit($id, Request $req) {
        if (array_value(c_user(), 'is_super_admin') != 1) {
            if (Term::where(['term_id' => $id, 'user_id' => array_value(c_user(), 'ID')])->count() == 0) {
                $response['errors'] = 'Permission Denied';
                $response['status'] = 'permissiondenied';
                return response()->json($response ,403);
            }
        }
        $term = Term::select(['terms.term_id', 'terms.name', 'terms.slug', 'terms.term_group', 'term_taxonomy.taxonomy', 'term_taxonomy.description', 'term_taxonomy.parent', 'term_taxonomy.count'])->join('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')->where(['terms.term_id' => $id, 'term_taxonomy.taxonomy' => $this->currentTaxonomy['taxonomy']])->first();
        
        if ($req->isMethod('post')) {
            $data = $req->all();
            $response = ['status' => [], 'errors' => []];
            $vArgs = [
                'name' => 'required',
                'featured_image' => 'file|max:1000|mimes:'.get_image_extensions('string'),
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

            $featuredImage = $data['_featured_image'];
            if ($req->file('featured_image')) {
                File::delete('public/assets/images/'.$data['_featured_image']);
                $image = $req->file('featured_image');
                $featuredImage = 'img-'.uniqid().time().'.'.$image->extension();
                $path = public_path('/assets/images');
                if(!File::exists($path)){
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $img = Image::make($image->path());
                $img->save($path.'/'.$featuredImage, 50);
            }

            if($term->update($data)) {
                TermTaxonomy::where(['term_id' => $id])->update([
                    'term_id' => $id,
                    'taxonomy' => $this->currentTaxonomy['taxonomy'],
                    'description' => array_value($data, 'description') ? array_value($data, 'description') : '',
                    'parent' => $data['parent_id'],
                    // 'count' => $termID,
    
                ]);
                if($featuredImage != '')
                    update_term_meta($id, '__featured_image', $featuredImage);

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
        if (array_value(c_user(), 'is_super_admin') != 1) {
            if ($id) {
                if (Term::where(['term_id' => $id,])->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
            else {
                if (Term::whereIn('term_id', $req->input('action_ids'))->count() == 0) {
                    return back()->with('errormsg', 'Permission Denied');
                }
            }
        }
        if ($id) {
            if (Term::where(['term_id' => $id])->count() > 0) {
                // Term::where(['parent_id' => $id])->update(['parent_id' => 0]);
                // $fImg = Term::select('featured_image')->where(['id' => $id])->first()->toArray()['featured_image'];
                // File::delete('public/assets/images/'.$fImg);
                $fImg = get_term_meta($id, '__featured_image', true);
                File::delete('public/assets/images/'.$fImg);
                Term::where(['term_id' => $id])->delete();
                TermTaxonomy::where(['term_id' => $id])->delete();
            }

            return back()->with('msg', 'Delete successfully');
        }
        else {
            
            if ($req->input('rec_action') == 'delete') {
                var_dump(Term::whereIn('term_id', $req->input('action_ids'))->count()); die;
                if (Term::whereIn('term_id', $req->input('action_ids'))->count() > 0) {
                    // Term::whereIn('parent_id', $req->input('action_ids'))->update(['parent_id' => 0]);
                    // $fImgs = Term::select('featured_image')->whereIn('id', $req->input('action_ids'))->get()->toArray();
                    // foreach ($fImgs as $key => $fImg) {
                    //     File::delete('public/assets/images/'.$fImg['featured_image']);
                    // }
                    // Term::whereIn('id', $req->input('action_ids'))->delete();

                    foreach($req->input('action_ids') as $fid) {
                        $fImg = get_term_meta($fid, '__featured_image', true);
                        File::delete('public/assets/images/'.$fImg);
                    }
                    Term::whereIn('term_id', $req->input('action_ids'))->delete();
                    TermTaxonomy::whereIn('term_id', $req->input('action_ids'))->delete();
                }
            }
            return back()->with('msg', 'Delete successfully');
        }
    }

    

}
