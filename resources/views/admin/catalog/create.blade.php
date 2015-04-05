@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{icon_type('catalog')}}"></i>
            Catalog
        </h1>
        <p class="description">Creation of the catalog</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li>
                <a href="{{ cms_route('collections.index') }}"><i class="{{icon_type('collections')}}"></i>Collections</a>
            </li>
            <li class="active">
                <i class="{{icon_type('catalog')}}"></i>
                <strong>catalog</strong>
            </li>
        </ol>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Create a catalog</h3>
    </div>
    <div class="panel-body">
        {!! Form::open([
            'method' => 'post',
            'url'    => cms_route('catalog.index', [$collectionId]),
            'class'  => 'form-horizontal'
        ]) !!}
            @include('admin.catalog.form', [
                'lang'          => null,
                'submit'        => trans('general.create'),
                'submitAndBack' => trans('general.create_n_close'),
                'icon'          => 'save'
            ])
        {!! Form::close() !!}
    </div>
</div>
@include('admin.catalog.scripts')
@endsection
