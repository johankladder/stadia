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
                <th scope="col">Calendar</th>
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
                        <a href="{{route('calendar.index', $item->getId())}}" class="btn btn-outline-primary">Calendar
                            ({{$item->calendarRanges()->count()}})</a>
                    </td>
                    <td>
                        <form action="{{ route('stadia-plants.destroy', $item->getId())}}" method="POST">
                            @method('delete')
                            @csrf
                            <button class="btn btn-outline-danger" type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <a href="{{ route('stadia-plants.sync') }}" class="btn btn-primary">
            Sync
        </a>

    </div>

@endsection
