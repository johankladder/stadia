<html>

<head>

    @if(!App::runningUnitTests())
        <link href={{ asset("css/app.css") }} rel="stylesheet"/>
        <script src="{{ mix('js/app.js') }}"></script>
    @endif

    <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
    <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>


    <title>
        @yield('title')
    </title>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="/stadia">Stadia</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

        </ul>
    </div>
</nav>
<div class="p-5">
    @if(session()->has('message'))
        <div class="alert {{session('alert') ?? 'alert-info'}}">
            {{ session('message') }}
        </div>
    @endif

    <div class="row">
        @hasSection('content-side')
            <div class="col-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body">
                        <div class="row border-bottom p-1 mb-3">
                            <div class="col">
                                <a class="btn btn-outline-dark" href="@yield('backUrl', url()->previous() )">Back</a>
                            </div>
                            <div class="col text-center">
                                <h3 class="card-title mb-3 font-weight-bold">
                                    @yield('title')
                                </h3>
                            </div>
                            <div class="col">

                            </div>
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row border-bottom p-1 mb-3">
                            <div class="col text-center">
                                <h3 class="card-title mb-3 font-weight-bold">
                                    @yield('title-side')
                                </h3>
                            </div>
                        </div>
                        @yield('content-side')
                    </div>
                </div>
            </div>
        @else

            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-dark text-light">
                        <div class="row">
                            <div class="col">
                                <a class="btn btn btn-light" href="@yield('backUrl', url()->previous() )">Back</a>
                            </div>
                            <div class="col text-center">
                                <h3 class="font-weight-bold mb-0">
                                    @yield('title')
                                </h3>
                            </div>
                            <div class="col">

                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>


</body>

</html>
