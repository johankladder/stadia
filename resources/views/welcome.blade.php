@extends('stadia::layouts.app')

@section('title', 'Home')

@section('content')

    <div class="row">
        <div class="col">
            <div class="card">
                <img class="card-img-top" style="height: 350px; object-fit: cover;"
                     src="{{ asset('johankladder/stadia/images/plant.jpeg') }}" alt="Card image cap">
                <div class="card-footer">
                    <a href="{{ route('stadia-plants.index') }}" class="btn btn-secondary btn-block">
                        Go to plants
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <img class="card-img-top" style="height: 350px; object-fit: cover;"
                     src="{{ asset('johankladder/stadia/images/users.jpeg') }}" alt="Card image cap">
                <div class="card-footer">
                    <a href="{{ route('user-information.index') }}" class="btn btn-secondary btn-block">
                        Go to userinformation
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection


