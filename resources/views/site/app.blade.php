<!DOCTYPE html>
<html lang="{{language()}}">
<head>
@include('site.partials.head')
</head>
<body>
<div id="root">
    @include('site.partials.header')
    <main id="main">
        <div id="content">
            @yield('content')
        </div>
        <!-- #content -->
    </main>
    <!-- #main -->
</div>
<!-- #root -->
@include('site.partials.footer')
</body>
</html>
