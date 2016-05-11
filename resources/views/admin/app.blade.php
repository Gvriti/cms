<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin._partials.head')
</head>
<body class="page-body {{$settings->get('body')}}{{Auth::guard('cms')->user()->hasLockScreen() ? ' lockscreen-page' : ''}}">
    <div id="container">
        @include('admin._partials.user_top')
    @if ($settings->get('horizontal_menu'))
        @include('admin._partials.horizontal_menu')
    @endif
        <div class="page-container">
        @if (! $settings->get('horizontal_menu'))
            @include('admin._partials.sidebar_menu')
        @endif
            <div class="main-content">
            @if (! $settings->get('horizontal_menu'))
                @include('admin._partials.user')
            @endif
                @yield('content')
                @include('admin._partials.footer')
            </div>
        </div>
    </div>
@if (session()->has('includeLockscreen'))
    @include('admin.lockscreen')
@endif
</body>
</html>
