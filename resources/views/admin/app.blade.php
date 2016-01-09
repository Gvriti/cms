<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.partials.head')
</head>
<body class="page-body {{$settings->get('body')}}{{AuthCms::get()->hasLockScreen() ? ' lockscreen-page' : ''}}">
    <div id="container">
        @include('admin.partials.user_top')
    @if ($settings->get('horizontal_menu'))
        @include('admin.partials.horizontal_menu')
    @endif
        <div class="page-container">
        @if (! $settings->get('horizontal_menu'))
            @include('admin.partials.sidebar_menu')
        @endif
            <div class="main-content">
            @if (! $settings->get('horizontal_menu'))
                @include('admin.partials.user')
            @endif
                @yield('content')
                @include('admin.partials.footer')
            </div>
        </div>
    </div>
@if (session()->has('includeLockscreen'))
    @include('admin.lockscreen')
@endif
</body>
</html>
