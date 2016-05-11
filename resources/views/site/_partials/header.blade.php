<header id="header">
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{site_url()}}">
                    <img src="{{asset('assets/site/images/logo.png')}}" alt="Logo">
                </a>
            </div>
            <!-- .navbar-header -->
            <div id="navbar" class="navbar-collapse collapse">
                @include('site._partials.pages')
            @if (! empty($languages))
                <ul class="nav navbar-nav navbar-right">
                @foreach ($languages as $key => $value)
                    <li{!!$key == language() ? ' class="active"' : ''!!}>
                        <a href="{{$value['url']}}">{{$value['name']}}</a>
                    </li>
                @endforeach
                </ul>
            @endif
            </div>
            <!-- #navbar -->
        </div>
        <!-- .container -->
    </nav>
    <!-- .navbar -->
</header>
<!-- #header -->