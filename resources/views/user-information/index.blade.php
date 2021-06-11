@extends('stadia::layouts.app')

@section('title', 'Userinformation')

@section('content')
    <div>

        <div class="row">
            <div class="col">
                <div class="card border-0 shadow-lg">
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            {{ \JohanKladder\Stadia\Models\Information\StadiaHarvestInformation::count() }} information
                            entries
                            gathered in total!
                        </div>
                        <div id="chart-harvest" style="height: 300px;">
                        </div>

                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-lg">

                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            {{ \JohanKladder\Stadia\Models\Information\StadiaLevelInformation::count() }} information
                            entries gathered in total!
                        </div>

                        <div id="chart-level" style="height: 300px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <div class="card border-0 shadow-lg">
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            Top 5 most harvested plants this month and previous month
                        </div>
                        <ul class="list-group list-group-numbered">
                            @foreach($mostHarvest as $stadiaPlant)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col font-weight-bold">
                                            {{$loop->index + 1}}.
                                        </div>
                                        <div class="col-6 text-center">
                                            <a href="{{route('calendar.index', $stadiaPlant->getId())}}"
                                               target="_blank">
                                                <h5 class="mb-0">
                                                    {{$stadiaPlant->name}}
                                                </h5>
                                            </a>

                                        </div>
                                        <div class="col">
                                            <h4 class="mb-0">
                                               <span class="badge badge-primary float-right">
                                                    {{$stadiaPlant->harvest_count}}
                                               </span>
                                            </h4>
                                        </div>
                                    </div>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col">
            </div>
        </div>


        <script>
            const chartHarvestInformation = new Chartisan({
                el: '#chart-harvest',
                url: "@chart('harvest_chart')",
                hooks: new ChartisanHooks()
                    .title("Harvest entries this year")
                    .tooltip()
                    .datasets(['line'])
            });

            const chartLevelInformation = new Chartisan({
                el: '#chart-level',
                url: "@chart('level_chart')",
                hooks: new ChartisanHooks()
                    .title("Stadia entries this year")
                    .tooltip()
                    .datasets(['line'])
            });
        </script>

    </div>
@endsection

