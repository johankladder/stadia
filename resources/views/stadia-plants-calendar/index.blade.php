@extends('stadia::layouts.app')

@section('title', 'Calendar of: ' . $plant->getId() )

@section('content')

    <div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('calendar.store', $plant->getId())}}" method="POST">
                    <div class="row">

                        <div class="col">
                            Date from:
                            <input class="form-control datepicker mt-2" placeholder="Select from" name="range_from"
                                   type="date">
                        </div>
                        <div class="col">
                            Date to
                            <input class="form-control datepicker mt-2" placeholder="Select from" name="range_to"
                                   type="date">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="form-select-country-code">Select country</label>
                        <select class="form-control" id="form-select-country-code" name="country_id">
                            <option label=" "></option>

                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-3">
                        <label for="form-select-country-code">Select climate code</label>
                        <select class="form-control" id="form-select-country-code">
                        </select>
                    </div>

                    <div class="mt-3">
                        @method('post')
                        @csrf
                        <button class="btn btn-outline-primary btn-block" type="submit">Create new date range</button>

                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header bg-primary text-light">
                Global calendar dates
            </div>
            <div class="card-body">
                @if(count($itemsGlobal) <= 0)
                    <span class="text-muted">No dates set yet! </span>
                @else
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Date from</th>
                            <th scope="col">Date to</th>
                            <th scope="col">Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($itemsGlobal as $item)
                            <tr>
                                <td>
                                    {{$item->getDateFrom()}}
                                </td>
                                <td>
                                    {{$item->getDateTo()}}
                                </td>
                                <td>

                                    <form action="{{ route('calendar.destroy', $item->id)}}" method="POST">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-outline-danger" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-primary text-light">
                Calendar dates per country
            </div>

            <div class="card-body">
                @if(count($itemsCountry) <= 0)
                    <span class="text-muted">No date ranges given yet...</span>
                @else

                    @foreach($itemsCountry as $countryName => $items)

                        <div class="card mb-3">
                            <div class="card-header">
                                {{$countryName}}
                            </div>

                            <table class="table ">
                                <thead>
                                <tr>
                                    <th scope="col">Date from</th>
                                    <th scope="col">Date to</th>
                                    <th scope="col">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            {{$item->getDateFrom()}}
                                        </td>
                                        <td>
                                            {{$item->getDateTo()}}
                                        </td>
                                        <td>
                                            <form action="{{ route('calendar.destroy', $item->id)}}" method="POST">
                                                @method('delete')
                                                @csrf
                                                <button class="btn btn-outline-danger" type="submit">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-primary text-light">
                Calendar dates per country and climate codes:
            </div>

            <div class="card-body">
                @empty($itemsClimateCodes)
                    <span class="text-muted">No date ranges given yet...</span>
                @else

                @endif
            </div>
        </div>

    </div>

@endsection

@section('title-side', 'Show calendar')
@section('content-side')
    <div>
        <div>
            <div class="form-group">
                <label for="form-select-country-code">Select country</label>
                <select class="form-control" id="form-select-country-code">
                    <option label=" "></option>

                    @foreach($countries as $country)
                        <option>{{$country->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="form-select-climate-code">Climate code</label>
                <select class="form-control" id="form-select-climate-code">
                    <option label=" "></option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Get calendar</button>
        </div>

        @empty($selectedCalendar)
        @else
            <div class="mt-3">
                <hr/>

                <div class="row">

                </div>
            </div>
        @endif
    </div>

@endsection



