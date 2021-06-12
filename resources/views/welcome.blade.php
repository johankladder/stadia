@extends('stadia::layouts.app')

@section('title', 'Home')

@section('content')

    <div class="row">
        <div class="col">
            <div class="card border-0 shadow-lg">
                <a href="{{ route('stadia-plants.index') }}">
                    <img class="card-img-top" style="height: 350px; object-fit: cover;"
                         src="{{ asset('johankladder/stadia/images/plant.jpeg') }}" alt="Card image cap">
                    <div class="card-footer">
                        <a href="{{ route('stadia-plants.index') }}" class="btn btn btn-outline-dark btn-block btn-lg">
                            Go to plants
                        </a>
                    </div>
                </a>
            </div>
        </div>


        <div class="col">
            <div class="card border-0 shadow-lg">
                <a href="{{ route('user-information.index') }}">
                    <img class="card-img-top" style="height: 350px; object-fit: cover;"
                         src="{{ asset('johankladder/stadia/images/users.jpeg') }}" alt="Card image cap">
                    <div class="card-footer">
                        <a href="{{ route('user-information.index') }}"
                           class="btn btn btn-outline-dark btn-block btn-lg">
                            Go to userinformation
                        </a>
                    </div>
                </a>
            </div>
        </div>

        <div class="col"/>

    </div>

@endsection


