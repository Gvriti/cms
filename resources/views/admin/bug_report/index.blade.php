@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="fa fa-bug"></i>
            Bug Report
        </h1>
        <p class="description">Send a bug report to the developers</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-gear"></i>
                <strong>Site Settings</strong>
            </li>
        </ol>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Bug report form</h3>
    </div>
    {!! Form::open([
        'url'    => cms_route('bugReport.send'),
        'class'  => 'form-horizontal'
    ]) !!}
        <div class="panel-body">
            <div class="form-group{{($error = $errors->first('title')) ? ' validate-has-error' : '' }}">
                <label class="col-sm-3 control-label">Title:</label>
                <div class="col-sm-5">
                    @if ($error)
                    <span class="text-danger">{{$error}}</span>
                    @endif
                    {!! Form::text('title', null, [
                        'class' => 'form-control',
                    ]) !!}
                    <div class="desc">A title of a bug should describe the issue clearly and a reader can tell what the bug is by just the title alone; this helps with triaging the bug.</div>
                </div>
            </div>
            <div class="form-group{{($error = $errors->first('description')) ? ' validate-has-error' : '' }}">
                <label class="col-sm-3 control-label">Description:</label>
                <div class="col-sm-5">
                    @if ($error)
                    <span class="text-danger">{{$error}}</span>
                    @endif
                    {!! Form::textarea('description', null, [
                        'class' => 'form-control',
                        'rows' => '8'
                    ]) !!}
                    <div class="desc">A detailed description of the bug. Make sure your description is reflecting what the problem is and where it is.</div>
                </div>
            </div>
        </div>

        <div class="form-group-separator"></div>

        <div class="form-group">
            <div class="col-sm-10 btn-action pull-right">
                <button type="submit" class="btn btn-secondary" title="{{ trans('general.update') }}">
                    <i class="fa fa-save"></i>
                </button>
            </div>
        </div>
    {!! Form::close() !!}
</div>

<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>

<script type="text/javascript">
$(function() {
    $('.select').select2({
        placeholder: 'Select type...',
        allowClear: true
    }).on('select2-open', function() {
        // Adding Custom Scrollbar
        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
    });
});
</script>
@endsection
