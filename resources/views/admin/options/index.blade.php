@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{icon_type($collection->type)}}"></i>
            {{$collection->title}}
        </h1>
        <p class="description">{{$collection->title}}: პარამეტრების სია</p>
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
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title ttc">პარამეტრები</h3>
        <div class="panel-options">
            <a href="#" data-toggle="panel">
                <span class="collapse-icon">&ndash;</span>
                <span class="expand-icon">+</span>
            </a>
        </div>
    </div>
    <div class="panel-body">
        <a href="{{ cms_route('options.create', [$collection->id]) }}" class="btn btn-secondary btn-icon-standalone">
            <i class="{{icon_type('options')}}"></i>
            <span>{{ trans('general.create') }}</span>
        </a>
        <a href="{{ cms_route($collection->type . '.index', [$collection->id]) }}" class="btn btn-blue btn-icon-standalone pull-right">
            <i class="fa fa-arrow-left"></i>
            <span>{{$collection->title}}</span>
        </a>
        <table class="table table-small-font table-bordered table-striped" id="items">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($items as $item)
                <tr id="item{{$item->id}}">
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->id }}</td>
                    <td>
                        <div class="btn-action">
                            {!! Form::open(['method' => 'post', 'url' => cms_route('options.visibility', [$item->id]), 'class' => 'visibility', 'id' => 'visibility' . $item->id]) !!}
                                <button type="submit" class="btn btn-{{$item->visible ? 'white' : 'gray'}}" title="{{trans('general.visibility')}}">
                                    <span class="fa fa-eye{{$item->visible ? '' : '-slash'}}"></span>
                                </button>
                            {!! Form::close() !!}
                            <a href="{{ cms_route('options.edit', [$item->collection_id, $item->id]) }}" class="btn btn-orange" title="{{trans('general.edit')}}">
                                <span class="fa fa-edit"></span>
                            </a>
                            {!! Form::open(['method' => 'delete', 'url' => cms_route('options.destroy', [$item->collection_id, $item->id]), 'class' => 'form-delete']) !!}
                            <button type="submit" class="btn btn-danger" data-id="{{ $item->id }}" title="{{trans('general.delete')}}">
                                <span class="fa fa-trash"></span>
                            </button>
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
$(function() {
    @include('admin.scripts.destroy')
});
</script>

@endsection
