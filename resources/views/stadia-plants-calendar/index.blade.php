@extends('stadia::layouts.app')

@section('title', 'Calendar of: ' . $plant->getName() ?: $plant->getId() )
@section('backUrl', route('stadia-plants.index') )

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
                    <div class="alert alert-danger">
                        No default dates set yet!
                    </div>
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
                                    {{date('d-m', strtotime($item->getDateFrom()))}}
                                </td>
                                <td>
                                    {{date('d-m', strtotime($item->getDateTo()))}}
                                </td>
                                <td>

                                    <form action="{{ route('calendar.destroy', $item->id)}}" method="POST">
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
                Calendar dates per country
            </div>

            <div class="card-body">
                @if(count($itemsCountry) <= 0)
                    <span class="text-muted">No date ranges given yet...</span>
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
                            <div id="collapse-{{str_replace(' ', '', $countryName)}}">

                                <table class="table">
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
                                                {{date('d-m', strtotime($item->getDateFrom()))}}
                                            </td>
                                            <td>
                                                {{date('d-m', strtotime($item->getDateTo()))}}
                                            </td>
                                            <td>
                                                <form action="{{ route('calendar.destroy', $item->id)}}" method="POST">
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
                Calendar dates per country and climate codes:
            </div>

            <div class="card-body">
                @empty($itemsClimateCode)
                    <span class="text-muted">No date ranges given yet...</span>
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
                        <div id="collapse-{{str_replace(' ', '', $countryName)}}">

                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Date from</th>
                                    <th scope="col">Date to</th>
                                    <th scope="col">Climate code</th>
                                    <th scope="col">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            {{date('d-m', strtotime($item->getDateFrom()))}}
                                        </td>
                                        <td>
                                            {{date('d-m', strtotime($item->getDateTo()))}}
                                        </td>
                                        <td>
                                            {{$item->climateCode->code}}
                                        </td>
                                        <td>
                                            <form
                                                action="{{ route('calendar.destroy', $item->id)}}"
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

@section('title-side', 'Check calendar ranges')
@section('content-side')
    <div>
        <div>
            <form action="{{ route('calendar.index', $plant)}}" method="GET">

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
                <button type="submit" class="btn btn-primary btn-block">Get calendar</button>
            </form>
        </div>


        @empty($selectedCalendar)
        @else
            <div class="">
                <hr/>
                <div class="row">
                    @if(count($selectedCalendar) > 0)
                        <div class="col">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Date from</th>
                                    <th scope="col">Date to</th>
                                    <th scope="col">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($selectedCalendar as $item)
                                    <tr>
                                        <td>
                                            {{date('d-m', strtotime($item->getDateFrom()))}}
                                        </td>
                                        <td>
                                            {{date('d-m', strtotime($item->getDateTo()))}}
                                        </td>
                                        <td>
                                            <form action="{{ route('calendar.destroy', $item->id)}}"
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
            <div class="card border-0 shadow-lg">
                <div class="">

                    @if(count($scatterInformation) <= 0)
                        <div class="alert alert-danger" role="alert">
                            Not information gathered yet...
                        </div>
                    @endif

                    @if(count($scatterInformation) > 0 && count($scatterInformation) < 100)
                        <div class="alert alert-warning" role="alert">
                            Not enough information gathered yet...
                        </div>
                    @endif

                    <canvas class="p-3" id="chart-calendar" height="100" width="100%">
                    </canvas>


                    <div class="alert alert-secondary m-3" role="alert">
                        Found {{count($scatterInformation)}} entries
                    </div>
                </div>
            </div>

        @endif
    </div>

    <script>
        var data = <?php echo $scatterInformation; ?>;
        var lineDate = [];
        var barChartData = {
            datasets: [
                {
                    type: 'scatter',
                    label: 'Entries',
                    backgroundColor: "red",
                    data: data,
                    order: 2,
                },
                {
                    type: 'line',
                    fill: false,
                    borderColor: 'blue',
                    label: 'Regression line',
                    backgroundColor: "blue",
                    data: lineDate,
                    order: 1,
                }
            ],
        };

        window.onload = function () {
            var ctx = document.getElementById("chart-calendar").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'scatter',
                data: barChartData,
                options: {
                    tooltips: {
                        enabled: false
                    },
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    precision: 0,
                                },
                            },
                        ],
                        xAxes: [
                            {
                                ticks: {
                                    precision: 0,
                                },
                            },
                        ],
                    }
                }
            });
        };
    </script>

@endsection





