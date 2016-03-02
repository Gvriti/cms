@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{$iconArticle = icon_type('articles')}}"></i>
            {{ $collection->type }}
        </h1>
        <p class="description">{{ $collection->description }}</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li>
                <a href="{{ cms_route('collections.index') }}"><i class="{{$iconColl = icon_type('collections')}}"></i>Collections</a>
            </li>
            <li class="active">
                <i class="{{$iconArticle}}"></i>
                <strong>{{ $collection->title }}</strong>
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-9 pull-right has-sidebar">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title ttc">{{ $collection->title }}</h3>
                <div class="panel-options">
                    <a href="{{cms_route('collections.edit', [$collection->id])}}">
                        <i class="fa fa-gear"></i>
                    </a>
                    <a href="#" data-toggle="panel">
                        <span class="collapse-icon">&ndash;</span>
                        <span class="expand-icon">+</span>
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <a href="{{ cms_route('articles.create', [$collection->id]) }}" class="btn btn-secondary btn-icon-standalone">
                    <i class="{{$iconArticle}}"></i>
                    <span>{{ trans('general.create') }}</span>
                </a>
                <button id="save-tree" class="btn btn-secondary btn-icon-standalone dn" disabled>
                    <i><b class="icon-var fa-save"></b></i>
                    <span>{{ trans('general.save') }}</span>
                </button>
                <div id="items">
                    <ul id="nestable-list" class="uk-nestable" data-uk-nestable="{maxDepth:1}">
                    @foreach ($items as $item)
                        <li id="item{{ $item->id }}" class="item" data-id="{{ $item->id }}" data-pos="{{$item->position}}">
                            <div class="uk-nestable-item clearfix">
                            @if ($collection->admin_order_by == 'position')
                                <div class="uk-nestable-handle"></div>
                            @endif
                                <div class="list-label"><a href="{{ cms_route('articles.edit', [$collection->id, $item->id]) }}">{{ $item->title }}</a></div>
                                <div class="btn-action togglable pull-right">
                                    <div class="btn btn-gray item-id disabled">#{{$item->id}}</div>
                                    <a href="#" class="movable btn btn-white" title="Move to collection" data-id="{{$item->id}}">
                                        <span class="{{$iconColl}}"></span>
                                    </a>
                                    {!! Form::open(['method' => 'post', 'url' => cms_route('articles.visibility', [$item->id]), 'class' => 'visibility', 'id' => 'visibility' . $item->id]) !!}
                                        <button type="submit" class="btn btn-{{$item->visible ? 'white' : 'gray'}}" data-id="{{ $item->id }}" title="{{trans('general.visibility')}}">
                                            <span class="fa fa-eye{{$item->visible ? '' : '-slash'}}"></span>
                                        </button>
                                    {!! Form::close() !!}
                                    <a href="{{ cms_route('files.index', ['articles', $item->id]) }}" class="btn btn-{{$item->files_id ? 'turquoise' : 'white'}}" title="{{trans('general.files')}}">
                                        <span class="{{icon_type('files')}}"></span>
                                    </a>
                                    <a href="{{ cms_route('articles.edit', [$collection->id, $item->id]) }}" class="btn btn-orange" title="{{trans('general.edit')}}">
                                        <span class="fa fa-edit"></span>
                                    </a>
                                    {!! Form::open(['method' => 'delete', 'url' => cms_route('articles.destroy', [$collection->id, $item->id]), 'class' => 'form-delete', 'data-id' => $item->id]) !!}
                                        <button type="submit" class="btn btn-danger" title="{{trans('general.delete')}}">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    {!! Form::close() !!}
                                </div>
                                <a href="#" class="btn btn-primary btn-toggle pull-right visible-xs">
                                    <span class="fa fa-toggle-left"></span>
                                </a>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                    {!! $items->links() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 content-sidebar pull-left">
        <a href="{{cms_route('collections.create', ['type' => $collection->type])}}" class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right">
            <i class="{{$iconColl}}"></i>
            <span>კოლექციის დამატება</span>
        </a>
        <ul class="list-unstyled bg">
        @foreach ($similarTypes as $item)
            <li{!!$item->id != $collection->id ? '' : ' class="active"'!!}>
                <a href="{{ cms_route($item->type . '.index', [$item->id]) }}">
                    <i class="fa fa-folder{{$item->id != $collection->id ? '' : '-open'}}-o"></i>
                    <span>{{$item->title}}</span>
                </a>
            </li>
        @endforeach
        </ul>
    </div>
</div>
@include('admin.scripts.move', ['route' => 'articles', 'list' => $similarTypes, 'id' => $collection->id, 'column' => 'collection_id'])
<script type="text/javascript">
$(function() {
@if ($collection->admin_order_by == 'position')
    positionable('{{ cms_route('articles.updatePosition') }}', '{{$collection->admin_sort}}');
@endif
});
</script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>
<script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>
@endsection
