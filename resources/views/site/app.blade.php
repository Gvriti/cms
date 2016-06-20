<!DOCTYPE html>
<html lang="{{language()}}">
<head>
@include('site._partials.head')
</head>
<body>
<div id="root">
    @include('site._partials.header')
    <main id="main">
        <div id="content">
            @yield('content')
        </div>
        <!-- #content -->
    </main>
    <!-- #main -->
</div>
<!-- #root -->
@include('site._partials.footer')
<script src="{{asset('assets/site/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/site/js/custom.js')}}"></script>
@if (Auth::guard('cms')->check())
<script src="{{ asset('assets/js/trans.js') }}"></script>
<div id="translations" data-trans-url="{{cms_route('translations.popup')}}" data-token="{{csrf_token()}}"></div>
@endif
</body>
</html>