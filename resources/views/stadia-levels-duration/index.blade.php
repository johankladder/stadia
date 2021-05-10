@extends('stadia::layouts.app')

@section('title', 'Durations of: ' . $stadiaLevel->getName() ?: $stadiaLevel->getId() )
@section('backUrl', route('stadia-levels.index', $stadiaLevel->stadiaPlant) )

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
                <form action="{{ route('durations.store', $stadiaLevel->getId())}}" method="POST">
                    <div class="row">
                        <div class="col">
                            Duration in days:
                            <input class="form-control datepicker mt-2" placeholder="Fill in a duration" name="duration"
                                   type="number">
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
                        <select class="form-control" id="form-select-climate-code" name="climate_code_id">
                            <option label=" "></option>

                            @foreach($climateCodes as $code)
                                <option value="{{$code->id}}">{{$code->code}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3">
                        @method('post')
                        @csrf
                        <button class="btn btn-outline-primary btn-block" type="submit">Create new duration</button>

                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header bg-primary text-light">
                Global durations of this stadia
            </div>
            <div class="card-body">
                @if(count($itemsGlobal) <= 0)
                    <div class="alert alert-danger">
                        No default durations set yet!
                    </div>
                @else
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Duration</th>
                            <th scope="col">Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($itemsGlobal as $item)
                            <tr>
                                <td>
                                    {{$item->duration}}
                                </td>
                                <td>

                                    <form action="{{ route('durations.destroy', $item->id)}}" method="POST">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
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
                Durations per country
            </div>

            <div class="card-body">
                @if(count($itemsCountry) <= 0)
                    <span class="text-muted">No durations set yet...</span>
                @else

                    @foreach($itemsCountry as $countryName => $items)

                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        {{$countryName}} ({{count($items)}})
                                    </div>
                                    <div class="col-sm-auto">
                                        <button class="btn btn-link" data-toggle="collapse"
                                                data-target="#collapse-{{str_replace(' ', '', $countryName)}}">Show
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse" id="collapse-{{str_replace(' ', '', $countryName)}}">

                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Duration</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                {{$item->duration}}
                                            </td>
                                            <td>
                                                <form action="{{ route('durations.destroy', $item->id)}}" method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-primary text-light">
                Durations per country and climate codes:
            </div>

            <div class="card-body">
                @empty($itemsClimateCode)
                    <span class="text-muted">No durations set yet...</span>
                @else
                    @foreach($itemsClimateCode as $countryName => $items)

                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        {{$countryName}} ({{count($items)}})
                                    </div>
                                    <div class="col-sm-auto">
                                        <button class="btn btn-link" data-toggle="collapse"
                                                data-target="#collapse-{{str_replace(' ', '', $countryName)}}">Show
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="collapse-{{str_replace(' ', '', $countryName)}}">

                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Climate code</th>
                                    <th scope="col">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            {{$item->duration}}
                                        </td>
                                        <td>
                                            {{$item->climateCode->code}}
                                        </td>
                                        <td>
                                            <form
                                                action="{{ route('durations.destroy', $item->id)}}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <button
                                                    class="btn btn-sm btn-outline-danger"
                                                    type="submit">
                                                    Remove
                                                </button>
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
    </div>


@endsection

@section('title-side', 'Show durations')
@section('content-side')
    <div>
        <div>
            <form action="{{ route('durations.index', $stadiaLevel)}}" method="GET">

                <div class="form-group">
                    <label for="form-select-country-code">Select country</label>
                    <select class="form-control" id="form-select-country-code" name="country">
                        <option label=" "></option>
                        @foreach($countries as $country)
                            <option
                                value="{{$country->id}}"
                            @empty($selectedCountry)
                                @else
                                @if($selectedCountry != null && ($country->id == $selectedCountry->id))
                                    {{'selected'}}
                                    @endif
                                @endif
                            >{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="form-select-climate-code">Climate code</label>
                    <select class="form-control" id="form-select-country-code" name="climateCode">
                        <option label=" "></option>
                        @foreach($climateCodes as $code)
                            <option
                                value="{{$code->id}}"
                            @empty($selectedClimateCode)
                                @else
                                @if($selectedClimateCode != null && ($code->id == $selectedClimateCode->id))
                                    {{'selected'}}
                                    @endif
                                @endif
                            >{{$code->code}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Get duration(s)</button>
            </form>
        </div>


        @empty($selectedDurations)
        @else
            <div class="">
                <hr/>
                <div class="row">
                    @if(count($selectedDurations) > 0)
                        <div class="col">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($selectedDurations as $item)
                                    <tr>
                                        <td>
                                            {{$item->duration}}
                                        </td>

                                        <td>
                                            <form action="{{ route('durations.destroy', $item->id)}}"
                                                  method="POST">
                                                @method('delete')
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="col">
                            <p class="text-danger">No dates are found for this country</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Show the scatter plot of the sowing to harvest ratio:  --}}

            @if($selectedCountry)
                <div class="card">
                    <div class="card-header">
                        Scatter plot of this plant in this country:
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            @endif

        @endif
    </div>

@endsection



