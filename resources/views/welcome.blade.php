@extends('stadia::layouts.app')

@section('title', 'Home')

@section('content')

    <div class="list-group">
        <a href="{{ route('stadia-plants.index') }}" class="list-group-item list-group-item-action">
            Plants (0)
        </a>
        <a href="{{ route('stadia-plants.index') }}" class="list-group-item list-group-item-action">
            Userdata
        </a>
        <a href="{{ route('stadia-plants.index') }}" class="list-group-item list-group-item-action">
            Userdata
        </a>
    </div>
@endsection
