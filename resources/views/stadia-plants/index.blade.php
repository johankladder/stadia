@extends('stadia::layouts.app')

@section('title', 'Stadiaplants')

@section('content')
    <div>
        <div class="d-flex justify-content-center">
            {{ $items->links() }}
        </div>
        <table class="table table-bordered">
            <thead class="">
            <tr>
                <th scope="col">Name</th>
                <th scope="col" style="width: 125px">Image</th>
                <th scope="col">Relations</th>
                <th scope="col">Support</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>
                        @empty($item->getName())
                            No name found...
                        @else
                            {{$item->getName()}}
                        @endif
                    </td>
                    <td>
                        <img src="{{$item->getReferencePlant() ? $item->getReferencePlant()->getImageUrl() : ""}}"
                             style="height: 100px; width: 100px; object-fit: cover; background: url(https://via.placeholder.com/100);">
                    </td>
                    <td>
                        <a href="{{route('calendar.index', $item->getId())}}" class="btn btn-outline-dark btn-block">
                            Calendar
                        </a>
                        <a href="{{route('stadia-levels.index', $item->getId())}}"
                           class="btn btn-outline-dark btn-block">
                            Levels ({{$item->stadiaLevels()->count()}})
                        </a>
                    </td>

                    <td class="text-muted font-italic">
                        <div class="row">
                            <div class="col">
                                Calendar:
                                <div class="border p-3 rounded text-center bg-light mt-1">

                                    @if($item->calendarRanges()->count() > 0)
                                        <span class="badge badge-pill badge-dark">
                                                Countries ({{$item->getSupportedCountries()->count()}} / {{\JohanKladder\Stadia\Models\Country::count()}})
                                            </span>
                                    @endif
                                    @if($item->calendarRanges()->whereNull('country_id')->count() > 0)
                                        <span class="badge badge-pill badge-success">
                                                Globally supported!
                                            </span>
                                    @endif
                                    @if($item->calendarRanges()->count() <= 0)
                                        <span class="badge badge-pill badge-danger">
                                        Globally unsupported!
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            @if($item->stadiaLevels()->count() > 0)
                                <div class="col">
                                    Levels:
                                    <div class="border p-3 rounded text-center bg-light mt-1">
                                        @if($item->durations()->whereNull('country_id')->count() >= $item->stadiaLevels()->count() && $item->stadiaLevels()->count() != 0)
                                            <span class="badge badge-pill badge-success">
                                                Globally supported!
                                            </span>
                                        @endif

                                        @if($item->durations()->whereNull('country_id')->count() < $item->stadiaLevels()->count() && $item->stadiaLevels()->count() != 0)
                                            <span class="badge badge-pill badge-danger">
                                                Globally unsupported ({{$item->durations()->whereNull('country_id')->count()}} / {{$item->stadiaLevels()->count()}})
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            @endif
                        </div>

                    </td>
                    <td>
                        <div class="h-100">
                            <form action="{{ route('stadia-plants.destroy', $item->getId())}}" method="POST">
                                @method('delete')
                                @csrf
                                <button class="btn btn-danger" type="submit">Remove</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $items->links() }}
        </div>

        <a href="{{ route('stadia-plants.sync') }}" class="btn btn-outline-dark">
            Sync
        </a>

    </div>

@endsection
