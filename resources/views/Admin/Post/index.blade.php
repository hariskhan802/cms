

@extends('Admin.Layout.layout')

@section('content')
    
	<div class="main-wrap  {{ $postType.'-wrap' }}">
        <form>
            <input type="hidden" name="post_type" value="{{ $postType }}">
            @if(session('msg'))
            <div class="card mb-4 border-left-success">
                <div class="card-body">
                    {{ session('msg') }}
                </div>
            </div>
            @endif

            @if(session('errormsg'))
            <div class="card mb-4 border-left-danger">
                <div class="card-body">
                    {{ session('errormsg') }}
                </div>
            </div>
            @endif
            <div class="btn-wrap">
                <a  href="{{ get_admin_post_type_url(['name' => 'add-post'], $postType) }}" class="btn btn-primary btn-icon-split  f-action-switcher add-new-record ">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Add New</span>
                </a>
            </div>
            <div class="table-wrap">
            <div class="card shadow mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ array_value(array_value($currentPostType, 'labels'), 'name')  }}</h6>
                </div>
                
                <div class="row c-row">

                    <div class="column c-column">

                        <div class="records-status-wrap">
                            <ul>
                                <li class=" {{ \Request::input('status') == '' ? 'active' : '' }} "><a href="{{ get_admin_post_type_url(['name' => 'posts'], $postType) }}">All</a></li>
                                <li class=" {{ \Request::input('status') == 'publish' ? 'active' : '' }} "><a href="{{ get_admin_post_type_url(['name' => 'posts'], $postType).'&status=publish' }}">Published</a></li>
                                <li class=" {{ \Request::input('status') == 'draft' ? 'active' : '' }} "><a href="{{ get_admin_post_type_url(['name' => 'posts'], $postType).'&status=draft' }}">Drafts</a></li>
                                <li class=" {{ \Request::input('status') == 'trash' ? 'active' : '' }} "><a href="{{ get_admin_post_type_url(['name' => 'posts'], $postType).'&status=trash' }}">Trash</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="column c-column">

                        <div class="search-wrap ">
                            <select class="rec-action" name="rec_action">
                                <option value="">-----------</option>
                                @if(\Request::input('status') != 'trash')
                                <option value="trash" data-form="{{ get_admin_post_type_url(['name' => 'delete-post'], $postType) }}">Trash</option>
                                @elseif(\Request::input('status') == 'trash')
                                <option value="delete" data-form="{{ get_admin_post_type_url(['name' => 'delete-post'], $postType) }}">Delete</option>
                                <option value="restore" data-form="{{ get_admin_post_type_url(['name' => 'restore-post'], $postType) }}">Restore</option>
                                @endif
                            </select>
                            
                        </div>
                    </div>
                     <div class="column c-column">

                        <div class="search-wrap ">
                            
                            <input type="text" name="search" placeholder="Search" value="{{ \Request::input('search') }}" class="pull-right">
                            
                        </div>
                    </div>
                </div>
                <div class="row c-row">
                    <div class="column c-column">
                        <div class="total-record">
                            <span>Total Records : {{ $totalRecords }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered c-table"  width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><input type="checkbox"  class="all-checked"></th>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    @if($postType != 'page')
                                    <th>Category</th>
                                    @else
                                    <th>Template</th>
                                    @endif
                                    <th>Featured Image</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @if($data->count() > 0)
                                    
                                    @foreach($data as $record)
                                        <tr>
                                            <td><input type="checkbox" name="action_ids[]" value="{{ $record->ID }}"></td>
                                            <td>{{ $record->ID }}</td>
                                            <td>{{ $record->post_title }}</td>
                                            <td>{{ $record->display_name }}</td>
                                            @if($postType != 'page')
                                            <td><p>{!! implode("<br> ", get_term_string_format($record->ID)) !!}</p></td>
                                            @else
                                            <td>{{ array_value(get_template(get_post_meta($record->ID, '__template_id', true)), 'title') }}</td>
                                            @endif
                                            <td><img src="{{ get_image(get_post_meta($record->ID, '__featured_image', true)) }}" width="50"></td>
                                            <td>{!! get_admin_panel_post_type_dates($record) !!}</td>
                                            <td class="action">
                                                @if(\Request::input('status') != 'trash')
                                                <a href="{{ get_admin_post_type_url(['name' => 'edit-post', 'id' => $record->ID], $postType) }}" class="edit-record"><i class="fa fa-pencil-alt"></i></a>
                                                @else
                                                <a href="{{ get_admin_post_type_url(['name' => 'restore-post', 'id' => $record->ID], $postType) }}">
                                                    <i class="fa fa-undo"></i>
                                                </a>
                                                @endif
                                                <a href="{{ get_admin_post_type_url(['name' => 'delete-post', 'id' => $record->ID], $postType) }}" class="{{ \Request::input('status') == 'trash' ? 'danger-delete' : '' }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr> 
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="nofound-td">There is not record</td>
                                        
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination">
                    {{ $data->appends(\Request::query())->links() }}
                    </div>
                </div>
            </div>
                            
            </div>
        </form>
    </div>

    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Confirm" below if you want to delete.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary confirm-btn" href="" >Confirm</a>
                </div>
            </div>
        </div>
    </div>

@endsection