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
    <header class="jumbotron">
        <h1>{{$current->title}}</h1>
        {!!$current->description!!}
    </header>
    <!-- .jumbotron -->
    <div id="items">
    @foreach ($items as $item)
        <div class="col-sm-3">
        @if ($item->file)
            <div class="img">
                <a href="{{$item->file}}">
                    <img src="{{$item->file}}" class="img-responsive" alt="{{$item->title}}">
                </a>
            </div>
            <!-- .img -->
        @endif
            <div class="title text-center">
                <h2>{{$item->title}}</h2>
            </div>
            <!-- .title -->
        </div>
        <!-- .col-sm-3 -->
    @endforeach
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
