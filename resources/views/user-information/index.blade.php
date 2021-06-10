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


        <script>
            const chartHarvestInformation = new Chartisan({
                el: '#chart-harvest',
                url: "@chart('harvest_chart')",
                hooks: new ChartisanHooks()
                    .title("Harvest entries this year")
                    .tooltip()
            });

            const chartLevelInformation = new Chartisan({
                el: '#chart-level',
                url: "@chart('level_chart')",
                hooks: new ChartisanHooks()
                    .title("Stadia entries this year")
                    .tooltip()
            });
        </script>

    </div>
@endsection

