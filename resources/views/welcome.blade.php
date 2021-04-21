@extends('stadia::layouts.app')

@section('title', 'Home')

@section('content')

    <div class="list-group">
        <a href="{{ route('stadia-plants.index') }}" class="list-group-item list-group-item-action">
            Plants ({{\JohanKladder\Stadia\Models\StadiaPlant::count()}})
        </a>
    </div>
@endsection
