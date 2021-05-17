@extends('stadia::layouts.app')

@section('title', 'Stadiaplants')

@section('content')
    <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col"># (reference)</th>
                <th scope="col">Reference table</th>
                <th scope="col">Name</th>
                <th scope="col">Relations</th>
                <th scope="col">Support</th>
                <th scope="col">Options</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>
                        {{$item->getReferenceId()}}
                    </td>
                    <td>
                        {{$item->getReferenceTable()}}
                    </td>
                    <td>
                        @empty($item->getName())
                            No name found...
                        @else
                            {{$item->getName()}}
                        @endif
                    </td>
                    <td>
                        <a href="{{route('calendar.index', $item->getId())}}" class="btn btn-outline-primary">
                            Calendar
                        </a>
                        <a href="{{route('stadia-levels.index', $item->getId())}}" class="btn btn-outline-primary">
                            Levels ({{$item->stadiaLevels()->count()}})
                        </a>
                    </td>

                    <td>
                        <div class="row">
                            <div class="col-sm mr-3">

                                <div class="card border-0 shadow-lg">
                                    <div class="p-3">
                                        <ul class="list-group list-group-flush">
                                            @if($item->calendarRanges()->count() > 0)
                                                <div class="list-group-item border-0">
                                                    <span class="badge badge-pill badge-primary">Countries ({{$item->getSupportedCountries()->count()}} / {{\JohanKladder\Stadia\Models\Country::count()}})</span>
                                                </div>
                                            @endif
                                            @if($item->calendarRanges()->whereNull('country_id')->count() > 0)
                                                <div class="list-group-item">
                                                    <div class="card mt-1 shadow-lg border-0">
                                                        <div class="card-header bg-success text-light">
                                                            Globally supported
                                                        </div>
                                                        <div class="p-3">
                                                            @foreach($item->calendarRanges as $range)

                                                                <div class="row">
                                                                    <div class="col">
                                                                        {{date('d-m', strtotime($range->getDateFrom()))}}
                                                                    </div>

                                                                    <div class="col text-right font-weight-bold">
                                                                        {{date('d-m', strtotime($range->getDateTo()))}}
                                                                    </div>
                                                                </div>

                                                            @endforeach
                                                        </div>

                                                    </div>

                                                </div>
                                            @endif
                                            @if($item->calendarRanges()->count() <= 0)
                                                <div class="list-group-item">
                                                    <span
                                                        class="badge badge-pill badge-danger">Globally unsupported!</span>
                                                </div>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @if($item->stadiaLevels()->count() > 0)
                                <div class="col-sm">
                                    <div class="card border-0 shadow-lg">
                                        <div class="card-body">
                                            <div class="card-title">
                                                Levels
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @if($item->durations()->whereNull('country_id')->count() >= $item->stadiaLevels()->count() && $item->stadiaLevels()->count() != 0)
                                                    <div class="list-group-item">
                                                    <span
                                                        class="badge badge-pill badge-success">Globally supported!</span>
                                                    </div>
                                                @endif

                                                @if($item->durations()->whereNull('country_id')->count() < $item->stadiaLevels()->count() && $item->stadiaLevels()->count() != 0)
                                                    <div class="list-group-item">
                                                        <span class="badge badge-pill badge-danger">Globally unsupported ({{$item->durations()->whereNull('country_id')->count()}} / {{$item->stadiaLevels()->count()}})</span>
                                                    </div>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </td>
                    <td>
                        <form action="{{ route('stadia-plants.destroy', $item->getId())}}" method="POST">
                            @method('delete')
                            @csrf
                            <button class="btn btn-danger" type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <a href="{{ route('stadia-plants.sync') }}" class="btn btn-outline-primary">
            Sync
        </a>

    </div>

@endsection
