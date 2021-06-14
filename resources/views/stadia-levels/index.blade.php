@extends('stadia::layouts.app')

@section('title', 'Levels of ' . $stadiaPlant->name)

@section('backUrl', route('stadia-plants.index') )

@section('content')
    <div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col" style="width: 125px">Image</th>
                <th scope="col">Durations</th>
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
                        <img src="{{$item->getReferenceLevel() ? $item->getReferenceLevel()->getImageUrl() : ""}}"
                             style="height: 100px; width: 100px; object-fit: cover;">
                    </td>
                    <td>
                        <a href="{{route('durations.index', $item->getId())}}" class="btn btn-outline-dark btn-block">
                            Durations
                        </a>
                    </td>
                    <td>
                        <div class="row ">
                            <div class="col text-muted font-italic">
                                Durations:
                                <div class="border p-3 rounded bg-light text-center mt-1">
                                    @if($item->durations()->count() > 0)
                                        <span class="badge badge-pill badge-dark">Countries ({{$item->getSupportedCountries()->count()}} / {{\JohanKladder\Stadia\Models\Country::count()}})</span>
                                    @endif
                                    @if($item->durations()->whereNull('country_id')->count() > 0)
                                        <span class="badge badge-pill badge-success">Globally supported!</span>
                                    @endif
                                    @if($item->durations()->count() <= 0)
                                        <span class="badge badge-pill badge-danger">Globally unsupported!</span>
                                    @endif
                                </div>
                            </div>
                        </div>
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

        <a href="{{ route('stadia-levels.sync', $stadiaPlant) }}" class="btn btn-outline-dark">
            Sync
        </a>

    </div>

@endsection
