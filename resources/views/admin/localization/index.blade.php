@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="fa fa-language"></i>
            Localization
        </h1>
        <p class="description">List of all translation</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-language"></i>
                <strong>Localization</strong>
            </li>
        </ol>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">List of all translation</h3>
        <div class="panel-options">
            <a href="#">
                <i class="fa fa-gear"></i>
            </a>
            <a href="#" data-toggle="panel">
                <span class="collapse-icon">&ndash;</span>
                <span class="expand-icon">+</span>
            </a>
        </div>
    </div>
    <div class="panel-body">
        <a href="{{ cms_route('localization.create') }}" class="btn btn-secondary btn-icon-standalone">
            <i class="fa fa-file"></i>
            <span>{{ trans('general.create') }}</span>
        </a>
        <table class="table table-striped table-bordered" id="items">
            <thead>
                <tr class="replace-inputs">
                    <th>Name</th>
                    <th>Title</th>
                    <th>Value</th>
                    <th>ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($items as $item)
                <tr id="item{{$item->id}}">
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->value }}</td>
                    <td>{{ $item->id }}</td>
                    <td>
                        <div class="btn-action">
                            <a href="{{ cms_route('localization.edit', $item->id) }}" class="btn btn-orange" title="{{trans('general.edit')}}">
                                <span class="fa fa-edit"></span>
                            </a>
                            {!! Form::open(['method' => 'delete', 'url' => cms_route('localization.destroy', $item->id), 'class' => 'form-delete']) !!}
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

    $('#items').dataTable({
        pageLength: 50,
        // 'order': [0, 'desc']
    }).yadcf([
        {column_number : 0, filter_type: 'text', filter_default_label : 'Type a name'},
        {column_number : 1, filter_type: 'text', filter_default_label : 'Type a title'},
        {column_number : 2, filter_type: 'text', filter_default_label : 'Type a value'},
        {column_number : 3, filter_type: 'text', filter_default_label : 'ID'}
    ]);
});
</script>

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/datatables/yadcf/jquery.dataTables.yadcf.js') }}"></script>
@endsection
