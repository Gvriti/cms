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
</body>
</html>
