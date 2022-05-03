@extends('Admin.Layout.layout')

@section('content')

    <div class="main-wrap post-type-form {{ $postType.'-wrap' }}">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ array_value(array_value($currentPostType, 'labels'), 'singular_name')  }}</h6>
            </div>
            <div class="card-body">
                <div class="form-wrap">
                    <form method="post" action="{{ \Request::route()->getName() == 'edit-post' ? get_admin_post_type_url(['name' => 'edit-post', 'id' => \Request::route()->parameter('id')], $currentPostType['post_type']) : get_admin_post_type_url(['name' => 'add-post'], $currentPostType['post_type'] ) }}">    
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" placeholder="Title" name="post_title" required>
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" placeholder="Slug" name="slug" >
                            <small class="error-msg"></small>
                        </div>
                        @if($postType != 'page')
                        <div class="form-group">
                            <label>Category</label>
                            <select name="cats[]"  class="form-control" multiple >
                                <option value="">Select Category</option>
                                {!! get_parent_child_categories_dropdown('0') !!}
                            </select>
                            <small class="error-msg"></small>
                        </div>
                        @endif
                        @if($postType == 'page')
                        <div class="form-group">
                            <label>Template</label>
                            <select name="template_id"  class="form-control" required>
                                <option value="">Select Template</option>
                                @if ($templates->count() > 0)
                                    @foreach ($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="error-msg"></small>
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="post_content" class="form-control" id="html-editor" placeholder="Content"></textarea>
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group">
                            <label>Excerpt</label>
                            <textarea name="post_excerpt" rows="4" class="form-control" placeholder="Excerpt"></textarea>
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group img-f-g">
                            <label>Featured Image</label>
                            <input type="file" name="featured_image" accept="image/*"   />
                            <small class="error-msg"></small>
                        </div>
                    

                        {{ csrf_field() }}
                        <input type="hidden" name="_status">
                        <input type="hidden" name="_featured_image">
                        
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-primary" name="submit" value="Save Draft">
                        <input type="submit" class="btn btn-primary" name="submit" value="Publish">
                        <div class="card mb-4 c-msg border-left-success">
                            <div class="card-body">
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        
    </div>
@endsection