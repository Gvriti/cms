@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{icon_type('options')}}"></i>
            პარამეტრები
        </h1>
        <p class="description">პარამეტრის რედაქტირება</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li>
                <a href="{{ cms_route($collection->type . '.index', [$collection->id]) }}"><i class="{{icon_type($collection->type)}}"></i>{{$collection->title}}</a>
            </li>
            <li class="active">
                <i class="{{icon_type('options')}}"></i>
                <strong>პარამეტრები</strong>
            </li>
        </ol>
    </div>
</div>
<div class="clearfix">
    <ul class="nav nav-tabs col-xs-8">
@if ($isMultiLang = count(languages()) > 1)
    @foreach ($items as $item)
        <li{!!language() != $item->language ? '' : ' class="active"'!!}>
            <a href="#item-{{$item->language}}" data-toggle="tab">
                <span class="visible-xs">{{$item->language}}</span>
                <span class="hidden-xs">{{language($item->language)}}</span>
            </a>
        </li>
    @endforeach
@else
    @foreach ($items as $item)
        <li class="active">
            <a href="#item-{{$item->language}}" data-toggle="tab">
                <span class="visible-xs"><i class="fa fa-home"></i></span>
                <span class="hidden-xs">
                    <i class="fa fa-home"></i> General
                </span>
            </a>
        </li>
    @endforeach
@endif
    </ul>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">პარამეტრის რედაქტირება</h3>
        <div class="panel-options">
            <a href="#" data-toggle="panel">
                <span class="collapse-icon">&ndash;</span>
                <span class="expand-icon">+</span>
            </a>
        </div>
    </div>
    <div class="tab-content">
    @foreach ($items as $item)
        <div class="tab-pane{{language() != $item->language ? '' : ' active'}}" id="item-{{$item->language}}">
            <div class="panel-body">
                {!! Form::model($item, [
                    'method'    => 'put',
                    'url'       => cms_route('options.update', [$item->collection_id, $item->id], $isMultiLang ? $item->language : null),
                    'class'     => 'form-horizontal '.$settings->get('ajax_form'),
                    'data-lang' => $item->language
                ]) !!}
                    @include('admin.options.form', [
                        'lang'          => '_' . $item->language,
                        'submit'        => trans('general.update'),
                        'submitAndBack' => trans('general.update_n_back'),
                        'icon'          => 'save'
                    ])
                {!! Form::close() !!}
            </div>
        </div>
    @endforeach
    </div>
</div>
@endsection
