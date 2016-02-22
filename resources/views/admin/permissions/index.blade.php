@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{$icon = icon_type('permissions')}}"></i>
            Permissions
        </h1>
        <p class="description">Set permissions for the selected cms user</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li>
                <a href="{{ cms_route('cmsUsers.index') }}"><i class="{{icon_type('cms_users')}}"></i>CMS Users</a>
            </li>
            <li class="active">
                <i class="{{$icon}}"></i>
                <strong>Permissions</strong>
            </li>
        </ol>
    </div>
</div>
{!! Form::model($current, [
    'url' => cms_route('permissions.store', [$user->id])
]) !!}
    <div class="panel panel-headerless">
        <div class="panel-body">
            <div class="member-form-add-header">
                <div class="row">
                    <div class="col-md-2 col-sm-4 pull-right-sm">
                        <div class="permissions">
                            <a href="{{cms_route('cmsUsers.edit', [$user->id])}}" class="btn btn-block btn-turquoise">{{ trans('general.back') }}</a>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-block btn-secondary">{{ trans('general.update') }}</button>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8">
                        <div class="user-img">
                            <img src="{{$user->photo}}" width="128" class="img-circle" alt="Photo" />
                        </div>
                        <div class="user-name">
                            <a href="{{cms_route('cmsUsers.edit', [$user->id])}}">{{$user->firstname}} {{$user->lastname}}</a>
                            <span>{{$user->role_text}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="member-form-inputs">
    @foreach ($routes as $group => $route)
        <div class="panel panel-default clearfix">
            <div class="panel-heading">
                <label>{{ucfirst($group)}}</label> - 
                <div class="multi-check">
                    <a href="#" class="check-all" data-group="{{$group}}">Check all</a> / 
                    <a href="#" class="uncheck-all" data-group="{{$group}}">Uncheck all</a> / 
                    <a href="#" class="check-toggle" data-group="{{$group}}">Toggle</a>
                </div>
            </div>
        @foreach ($route as $name)
            @if (! in_array($name, $namesDisallowed))
            <div class="panel-body col-xs-6 col-sm-4 col-md-3">
                <label><strong>{{ucfirst(implode(' ', explode('.', substr($name, strpos($name, '.') + 1))))}}</strong></label>
                <input type="checkbox" name="permissions[][{{$group}}]" value="{{$name}}"{{in_array($name, $current) ? ' checked' : ''}} class="{{$group}} icheck" id="{{$name}}">
            </div>
            @endif
        @endforeach
        </div>
    @endforeach
        <div class="panel panel-default">
            <div class="btn-action text-center">
                <button type="submit" class="btn btn-secondary" title="{{ trans('general.update') }}">
                    <i class="fa fa-save"></i>
                </button>
                <a href="{{ cms_route('cmsUsers.edit', [$user->id]) }}" class="btn btn-blue" title="{{ trans('general.back') }}">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>
{!! Form::close() !!}
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    // Style Checkbox
    $('input.icheck').iCheck({
        checkboxClass: 'icheckbox_square-red'
    });

    $('.member-form-inputs .check-all').on('click', function(e) {
        e.preventDefault();

        var groupName = $(this).data('group');

        $('.member-form-inputs input.' + groupName).each(function() {
            $(this).iCheck('check');
        })
    });

    $('.member-form-inputs .uncheck-all').on('click', function(e) {
        e.preventDefault();

        var groupName = $(this).data('group');

        $('.member-form-inputs input.' + groupName).each(function() {
            $(this).iCheck('uncheck');
        })
    });

    $('.member-form-inputs .check-toggle').on('click', function(e) {
        e.preventDefault();

        var groupName = $(this).data('group');

        $('.member-form-inputs input.' + groupName).each(function() {
            $(this).iCheck('toggle');
        })
    });
})
</script>
@endsection
