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
                        <button class="btn btn-outline-dark btn-block" type="submit">Create new duration</button>

                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header  bg-dark text-light">
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
            <div class="card-header  bg-dark text-light">
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
                            <div id="collapse-{{str_replace(' ', '', $countryName)}}">

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
            <div class="card-header  bg-dark text-light">
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
                        <div id="collapse-{{str_replace(' ', '', $countryName)}}">

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
                <button type="submit" class="btn btn-outline-dark btn-block">Get duration(s)</button>
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
                            <p class="text-danger">No durations are found for this country</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-lg">
                <div class="">

                    @if(count($scatterInformation) <= 0)
                        <div class="alert alert-danger" role="alert">
                            No information gathered yet...
                        </div>
                    @endif

                    @if(count($scatterInformation) > 0 && count($scatterInformation) < 1000)
                        <div class="alert alert-warning" role="alert">
                            Not enough information gathered yet...
                        </div>
                    @endif

                    <canvas class="p-3" id="chart-durations" height="100" width="100%">
                    </canvas>

                    <div class="alert alert-info m-3" role="alert">
                        Regression information:
                        <ul class="mt-1">
                            <li>Intercept: {{$lineInformation['intercept']}}</li>
                            <li>Slope: {{$lineInformation['slope']}}</li>
                            <li>r2 based on 70/30: {{$lineInformation['r2']}}</li>
                        </ul>
                    </div>


                    <div class="alert alert-secondary m-3" role="alert">
                        Found {{count($scatterInformation)}} entries
                    </div>
                </div>
            </div>
        @endif
    </div>


    <script>
        var data = <?php echo $scatterInformation; ?>;
        var lineDate = <?php echo $lineInformation['line-values'] ?>;
        var barChartData = {
            datasets: [
                {
                    type: 'scatter',
                    label: 'Datapoints',
                    backgroundColor: "#5600e1",
                    data: data,
                    order: 2,
                },
                {
                    type: 'line',
                    fill: false,
                    borderColor: 'rgba(0,0,0,0.67)',
                    label: 'Regression line',
                    backgroundColor: "red",
                    data: lineDate,
                    order: 1,
                }
            ],
        };

        window.onload = function () {
            var ctx = document.getElementById("chart-durations").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'scatter',
                data: barChartData,

                options: {
                    scales: {
                        xAxes: [
                            {
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Day of year started'
                                },
                                ticks: {
                                    precision: 0,
                                    beginAtZero: true,
                                    max: 365
                                },
                            },
                        ],
                        yAxes: [
                            {
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Delta completed'
                                },
                                ticks: {
                                    precision: 0,
                                    beginAtZero: true,
                                    max: 500,
                                },
                            },
                        ],
                    }
                }
            });
        };
    </script>

@endsection



