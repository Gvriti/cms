@extends('site.app')
@section('content')
<div id="breadcrumb">
    <div class="container">
        @include('site._partials.breadcrumb')
    </div>
    <!-- .container -->
</div>
<!-- #breadcrumb -->
<div class="container">
    <article id="item" class="jumbotron">
        <div class="img">
        @if($current->image)
            <img src="{{$current->image}}" class="img-responsive" alt="{{$current->title}}">
        @endif
        </div>
        <!-- .img -->
        <div class="content">
            <header class="heading">
                <h1>{{$current->title}}</h1>
            </header>
            <!-- .heading -->
            <div class="text">
                {!!$current->content!!}
            </div>
            <!-- .text -->
        @if (! empty($files['mixed']))
            <div class="attached files">
                <ul class="list-unstyled">
                @foreach ($files['mixed'] as $item)
                    <li>
                        <a href="{{$item->file}}" target="_blank">{{$item->title}}</a>
                    </li>
                @endforeach
                </ul>
            </div>
            <!-- .files -->
        @endif
        @if (! empty($files['images']))
            <div class="attached images">
            @foreach ($files['images'] as $item)
                <div class="col-md-3 item">
                    <a href="{{$item->file}}" class="img-pop" rel="photo" title="{{$item->title}}">
                        <img src="{{$item->file}}" width="270" height="180" alt="{{$item->title}}">
                    </a>
                </div>
                <!-- .col-md-3 -->
            @endforeach
            </div>
            <!-- .images -->
        @endif
        </div>
        <!-- .content -->
    </article>
    <!-- #item -->
    <form action="{{site_url($current->slug)}}" method="GET">
        <div class="input-group">
            <input type="text" name="q" class="form-control" value="{{request('q')}}">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-primary">Go!</button>
            </span>
        </div>
        <!-- .input-group -->
    </form>
</div>
<!-- .container -->
@endsection
