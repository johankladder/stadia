@extends('stadia::layouts.app')

@section('title', 'Levels of ' . $stadiaPlant->name)

@section('content')
    <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col"># (reference)</th>
                <th scope="col">Reference table</th>
                <th scope="col">Name</th>
                <th scope="col">Calendar</th>
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
                    </td>
                    <td>

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

        <a href="{{ route('stadia-levels.sync', $stadiaPlant) }}" class="btn btn-outline-primary">
            Sync
        </a>

    </div>

@endsection
