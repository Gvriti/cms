@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="fa fa-gear"></i>
            Site Settings
        </h1>
        <p class="description">List of the site settings</p>
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
        <h3 class="panel-title">Site settings form</h3>
    </div>
@if (! empty($siteSettings))
    {!! Form::open([
        'method' => 'put',
        'url'    => cms_route('siteSettings.update'),
        'class'  => 'form-horizontal',
        'id'     => 'form-update'
    ]) !!}
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">Email:</label>
                <div class="col-sm-5">
                    {!! Form::text('email', $siteSettings->email, [
                        'class' => 'form-control',
                    ]) !!}
                    <span class="description">Messages from users will be sent to this email address.</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Phone:</label>
                <div class="col-sm-5">
                    {!! Form::text('phone', $siteSettings->phone, [
                        'class' => 'form-control',
                    ]) !!}
                    <span class="description">Phone number that will be displayed on the website.</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Date Format:</label>
                <div class="col-sm-5">
                    <div>
                        {!! Form::radio('date_format', 'd F Y', $siteSettings->date_format == 'd F Y', [
                            'class' => 'cbr cbr-success'
                        ]) !!}
                        <label>{{date('d F Y')}}</label>
                    </div>
                    <div>
                        {!! Form::radio('date_format', 'F d, Y', $siteSettings->date_format == 'F d, Y', [
                            'class' => 'cbr cbr-success'
                        ]) !!}
                        <label>{{date('F d, Y')}}</label>
                    </div>
                    <div>
                        {!! Form::radio('date_format', 'd M Y', $siteSettings->date_format == 'd M Y', [
                            'class' => 'cbr cbr-success'
                        ]) !!}
                        <label>{{date('d M Y')}}</label>
                    </div>
                    <div>
                        {!! Form::radio('date_format', 'M d, Y', $siteSettings->date_format == 'M d, Y', [
                            'class' => 'cbr cbr-success'
                        ]) !!}
                        <label>{{date('M d, Y')}}</label>
                    </div>
                    <div>
                        {!! Form::radio('date_format', 'd.m.Y', $siteSettings->date_format == 'd.m.Y', [
                            'class' => 'cbr cbr-success'
                        ]) !!}
                        <label>{{date('d.m.Y')}}</label>
                    </div>
                    <div class="form-inline">
                        {!! Form::radio('date_format', '', ! in_array($siteSettings->date_format, $dateFormatStatic), [
                            'class' => 'cbr cbr-success'
                        ]) !!}
                        <label>Custom format:</label>
                        <input type="text" name="date_format_custom" class="form-control" value="{{in_array($siteSettings->date_format, $dateFormatStatic) ? 'Y-m-d' : $siteSettings->date_format}}">
                        <div class="desc">Read more about <a href="http://www.w3schools.com/php/func_date_date.asp" target="_blank">date format</a>, <a href="http://php.net/date" target="_blank">date format</a></div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Faceebook page:</label>
                <div class="col-sm-5">
                    {!! Form::text('facebook', $siteSettings->facebook, [
                        'class' => 'form-control',
                    ]) !!}
                    <span class="description">Paste faceebook page url.</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Twitter page:</label>
                <div class="col-sm-5">
                    {!! Form::text('twitter', $siteSettings->twitter, [
                        'class' => 'form-control',
                    ]) !!}
                    <span class="description">Paste twitter page url.</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Googleplus page:</label>
                <div class="col-sm-5">
                    {!! Form::text('googleplus', $siteSettings->googleplus, [
                        'class' => 'form-control',
                    ]) !!}
                    <span class="description">Paste google plus page url.</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Map:</label>
                <div class="col-sm-5">
                    {!! Form::text('map', $siteSettings->map, [
                        'class' => 'form-control',
                    ]) !!}
                    <span class="description">Paste map url.</span>
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
@endif
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
