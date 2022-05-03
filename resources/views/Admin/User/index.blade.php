

@extends('Admin.Layout.layout')

@section('content')

	<div class="main-wrap {{ $name.'-wrap' }}">
        <form>
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
                <a data-toggle="modal" data-target="#add-edit-modal" data-form="{{ route('add-'.word_format($name)) }}" class="btn btn-primary btn-icon-split  f-action-switcher add-new-record ">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Add New</span>
                </a>
            </div>
            <div class="table-wrap">
            <div class="card shadow mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ word_format($name, 'cPlural')  }}</h6>
                </div>
                
                <div class="row c-row">

                    <div class="column c-column">

                        
                    </div>
                    <div class="column c-column">

                        <div class="search-wrap ">
                            <select class="rec-action" name="rec_action">
                                <option value="">-----------</option>
                                <option value="delete" data-form="{{ route('delete-'.word_format($name)) }}">Delete</option>
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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Image</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($data->count() > 0)
                                    
                                    @foreach($data as $record)
                                        <tr>
                                            <td><input type="checkbox" name="action_ids[]" value="{{ $record->id }}"></td>
                                            <td>{{ $record->ID }}</td>
                                            <td>{{ $record->display_name }}</td>
                                            <td>{{ $record->user_email }}</td>
                                            <td><img src="{{ get_user_image($record->image) }}" width="50"></td>
                                            <td>{{-- $record->role->role --}}</td>
                                            <td>{!! get_admin_panel_user_dates($record) !!}</td>
                                            <td class="action">
                                                @if(\Request::input('status') != 'trash')
                                                <a href="{{ route('edit-'.word_format($name), $record->ID) }}" class="edit-record"><i class="fa fa-pencil-alt"></i></a>
                                                @else
                                                <a href="{{ route('restore-'.word_format($name), $record->ID) }}">
                                                    <i class="fa fa-undo"></i>
                                                </a>
                                                @endif
                                                <a href="{{ route('delete-'.word_format($name), $record->ID) }}" class="danger-delete">
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

    @include('Admin.'.word_format($name, 'ucfirst').'.add-edit')
@endsection