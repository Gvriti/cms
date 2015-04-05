@extends('site.app')
@section('content')
<div id="breadcrumb">
    <div class="container">
        @include('site.partials.breadcrumb')
    </div>
    <!-- .container -->
</div>
<!-- #breadcrumb -->
<div class="container">
    <header class="heading">
        <h1>{{$current->title}}</h1>
    </header>
    <!-- .heading -->
    <div id="items">

    </div>
    <!-- #items -->
@if ($items->lastPage() > 1)
    <div id="pager">
        {!! $items->render() !!}
    </div>
    <!-- #pager -->
@endif
</div>
<!-- .container -->
@endsection
