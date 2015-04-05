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
    <div id="feedback" class="jumbotron">
        <header class="heading">
            <h1>{{$current->title}}</h1>
        </header>
        <!-- .heading -->
    @if ($current->content)
        <div class="text">
            {!!$current->content!!}
        </div>
        <!-- .text -->
    @endif
    @if ($alert = session()->get('alert'))
        <div class="alert alert-{{$alert['result'] ? 'success' : 'danger'}}">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{$app['trans']->get($alert['message'])}}
        </div>
    @endif
        <div id="feedback">
            <form action="{{site_route('feedback')}}" method="POST">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::text('name', null, [
                                'class' => 'form-control',
                                'placeholder' => $app['trans']->get('name')
                            ]) !!}
                            @if ($errors->has('name'))
                            <div class="text-danger">{{$errors->first('name')}}</div>
                            @endif
                        </div>
                        <!-- .form-group -->
                        <div class="form-group">
                            {!! Form::text('email', null, [
                                'class' => 'form-control',
                                'placeholder' => $app['trans']->get('email')
                            ]) !!}
                            @if ($errors->has('email'))
                            <div class="text-danger">{{$errors->first('email')}}</div>
                            @endif
                        </div>
                        <!-- .form-group -->
                        <div class="form-group">
                            {!! Form::text('phone', null, [
                                'class' => 'form-control',
                                'placeholder' => $app['trans']->get('phone')
                            ]) !!}
                            @if ($errors->has('phone'))
                            <div class="text-danger">{{$errors->first('phone')}}</div>
                            @endif
                        </div>
                        <!-- .form-group -->
                        <div class="form-group">
                            <div class="clearfix">
                                <input type="text" name="captcha" autocomplete="off" placeholder="{{$app['trans']->get('enter_code')}}" class="form-control pull-left code">
                                <img src="{{captcha_src('flat')}}" height="40" id="captcha-img" alt="captcha">
                                <a href="#" id="captcha-reload">
                                    <img src="{{asset('assets/site/images/reload.png')}}" width="20" height="20" alt="reload">
                                </a>
                            </div>
                            @if ($errors->has('captcha'))
                            <span class="text-danger">{{$errors->first('captcha')}}</span>
                            @endif
                        </div>
                        <!-- .form-group -->
                    </div>
                    <!-- .col-md-6 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::textarea('text', null, [
                                'class' => 'form-control',
                                'placeholder' => $app['trans']->get('text')
                            ]) !!}
                            @if ($errors->has('text'))
                            <span class="text-danger">{{$errors->first('text')}}</span>
                            @endif
                        </div>
                        <!-- .form-group -->
                    </div>
                    <!-- .col-md-6 -->
                </div>
                <!-- .row -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{$app['trans']->get('send')}}</button>
                </div>
                <!-- .form-group -->
            </form>
        </div>
        <!-- #feedback -->
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
    <!-- #feedback -->
</div>
<!-- .container -->
<script type="text/javascript">
$(function(){
@if ($alert || $errors->hasBag())
    $('html, body').animate({
        scrollTop: $('#feedback').offset().top
    }, 0);
@endif

    var i = 0;
    $('#captcha-reload').on('click', function(e) {
        e.preventDefault();
        i++;

        var captcha = '{{captcha_src('flat')}}' + i;

        $('#captcha-img').attr('src', captcha);
    });
});
</script>
@endsection
