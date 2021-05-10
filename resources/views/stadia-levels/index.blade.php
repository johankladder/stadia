@extends('stadia::layouts.app')

@section('title', 'Levels of ' . $stadiaPlant->name)

@section('backUrl', route('stadia-plants.index') )

@section('content')
    <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col"># (reference)</th>
                <th scope="col">Reference table</th>
                <th scope="col">Name</th>
                <th scope="col">Durations</th>
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
                        <a href="{{route('durations.index', $item->getId())}}" class="btn btn-outline-primary">
                            Durations
                        </a>
                    </td>
                    <td>
                        @if($item->durations()->count() > 0)
                            <div class="row">
                                <span class="badge badge-pill badge-primary">Countries ({{$item->getSupportedCountries()->count()}} / {{\JohanKladder\Stadia\Models\Country::count()}})</span>
                            </div>
                        @endif
                        @if($item->durations()->whereNull('country_id')->count() > 0)
                            <div class="row mt-1">
                                <span class="badge badge-pill badge-success">Globally supported!</span>
                            </div>
                        @endif
                        @if($item->durations()->count() <= 0)
                            <div class="row mt-1">
                                <span class="badge badge-pill badge-danger">Globally unsupported!</span>
                            </div>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('stadia-levels.destroy', $item->getId())}}" method="POST">
                            @method('delete')
                            @csrf
                            <button class="btn btn-danger" type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <a href="{{ route('stadia-levels.sync', $stadiaPlant) }}" class="btn btn-outline-primary">
            Sync
        </a>

    </div>

@endsection
