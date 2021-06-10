@extends('stadia::layouts.app')

@section('title', 'Userinformation')

@section('content')
    <div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Growing information
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            {{ \JohanKladder\Stadia\Models\Information\StadiaHarvestInformation::count() }} information
                            entries
                            gathered in total!
                        </div>
                        <div id="chart" style="height: 300px;">
                        </div>

                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Stadia information
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            {{ \JohanKladder\Stadia\Models\Information\StadiaLevelInformation::count() }} information
                            gathered in total!
                        </div>

                        <div class="mt-3">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
        <!-- Chartisan -->
        <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
        <script>
            const chart = new Chartisan({
                el: '#chart',
                url: "@chart('harvest_chart')",
            });
        </script>

    </div>
@endsection

