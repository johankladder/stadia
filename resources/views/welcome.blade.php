@extends('stadia::layouts.app')

@section('title', 'Home')

@section('content')

    <div class="list-group">
        <a href="{{ route('stadia-plants.index') }}" class="list-group-item list-group-item-action">
            Plants ({{\JohanKladder\Stadia\Models\StadiaPlant::count()}})
        </a>
        <a href="{{ route('user-information.index') }}" class="list-group-item list-group-item-action">
            Userinformation
        </a>
    </div>
@endsection


