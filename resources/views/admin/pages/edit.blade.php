@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{icon_type('pages')}}"></i>
            Pages
        </h1>
        <p class="description">Edit page</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li>
                <a href="{{ cms_route('menus.index') }}"><i class="{{icon_type('menus')}}"></i>Menus</a>
            </li>
            <li class="active">
                <i class="{{icon_type('pages')}}"></i>
                <strong>Pages</strong>
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
    <ul class="nav nav-tabs col-xs-4 right-aligned">
        <li>
            <a href="{{cms_route('files.index', ['pages', $item->id])}}">
                <span class="visible-xs"><i class="fa fa-files-o"></i></span>
                <div class="hidden-xs btn-icon-standalone">
                    <i class="fa fa-files-o"></i> {{trans('general.files')}}
                </div>
            </a>
        </li>
    </ul>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Edit page</h3>
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
                    'url'       => cms_route('pages.update', [$item->menu_id, $item->id], $isMultiLang ? $item->language : null),
                    'class'     => 'form-horizontal '.$settings->get('ajax_form'),
                    'data-lang' => $item->language
                ]) !!}
                    @include('admin.pages.form', [
                        'menuId'        => $item->menu_id,
                        'collection_id' => $item->collection_id,
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
@include('admin.pages.scripts')
@endsection
