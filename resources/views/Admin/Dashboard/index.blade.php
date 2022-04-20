

@extends('Admin.Layout.layout')

@section('content')

	<div class="main-wrap  {{ $name.'-wrap' }}">
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
    </div>

@endsection