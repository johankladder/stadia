<html>

<head>
    <link href={{ asset("css/app.css") }} rel="stylesheet"/>

    @if(!App::runningUnitTests())
        <script src="{{ mix('js/app.js') }}"></script>
    @endif

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

    <div>
        <a href="@yield('backUrl', url()->previous() )">Back</a>
    </div>

    <div class="row mt-3">
        @hasSection('content-side')
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        @yield('title')
                    </div>
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        @yield('title-side')
                    </div>
                    <div class="card-body">
                        @yield('content-side')
                    </div>
                </div>
            </div>
        @else

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @yield('title')
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
