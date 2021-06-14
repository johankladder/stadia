@extends('stadia::layouts.app')

@section('title', 'Userinformation')

@section('content')
    <div>

        <div class="row">
            <div class="col">
                <div class="card border-0 shadow-lg">
                    <div class="card-body">
                        <div id="chart-harvest" style="height: 300px;">
                        </div>

                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body">
                        <div id="chart-level">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body">
                        <h5 class="mb-3 font-weight-bold">
                            Top 5 most harvested plants this month and previous month
                        </h5>
                        <ul class="list-group list-group-flush">
                            @foreach($mostHarvest as $stadiaPlant)
                                <li class="list-group-item {{$loop->index == 0 ? 'list-group-item-dark' : ''}}">
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
                                               <span class="badge badge-secondary float-right">
                                                    {{$stadiaPlant->harvest_count}}
                                               </span>
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>


                    </div>
                    @if(count($mostHarvest) < 5)
                        <div class="alert alert-warning m-3" role="alert">
                            Not enough entries to show a top 5...
                        </div>
                    @endif
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body">
                        <div id="chart-level-monthly" style="min-height: 300px">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            const chartHarvestInformation = new Chartisan({
                el: '#chart-harvest',
                url: "@chart('harvest_chart')",
                hooks: new ChartisanHooks()
                    .title("Harvest entries this year")
                    .tooltip()
                    .colors(['rgba(0,0,0,0.67)', '#5600e1'])
                    .datasets([{type: 'bar', fill: false}, {type: 'line', fill: false}])
            });

            const chartLevelInformation = new Chartisan({
                el: '#chart-level',
                url: "@chart('level_chart')",
                hooks: new ChartisanHooks()
                    .title("Stadia entries this year")
                    .tooltip()
                    .colors(['rgba(0,0,0,0.67)', '#5600e1'])
                    .datasets([{type: 'bar', fill: false}, {type: 'line', fill: false}])
            });

            const chartLevelMonthlyInformation = new Chartisan({
                el: '#chart-level-monthly',
                url: "@chart('level_monthly_chart')",
                hooks: new ChartisanHooks()
                    .title("Stadia entries this month")
                    .tooltip()
                    .colors(['rgba(0,0,0,0.67)', '#5600e1'])
                    .datasets(['line'])
            });
        </script>

    </div>
@endsection

