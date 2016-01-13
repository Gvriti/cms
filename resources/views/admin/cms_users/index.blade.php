@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="fa fa-user-secret"></i>
            CMS Users
        </h1>
        <p class="description">List of all cms users</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-user-secret"></i>
                <strong>CMS Users</strong>
            </li>
        </ol>
    </div>
</div>
<ul class="nav nav-tabs">
    <li{!!request()->has('role') ? '' : ' class="active"'!!}>
        <a href="{{cms_route('cmsUsers.index')}}">All CMS Users</a>
    </li>
@foreach (user_roles() as $key => $value)
    <li{!!request('role') == "$key" ? ' class="active"' : ''!!}>
        <a href="{{cms_route('cmsUsers.index', ['role' => $key])}}">{{$value}}</a>
    </li>
@endforeach
</ul>
<div class="tab-content clearfix">
    <div class="pull-left">
        <a href="{{ cms_route('cmsUsers.create') }}" class="btn btn-secondary btn-icon-standalone">
            <i class="fa fa-user-plus"></i>
            <span>{{ trans('general.create') }}</span>
        </a>
    </div>
    <table class="table table-hover members-table middle-align">
        <thead>
            <tr>
                <th></th>
                <th>Name and Role</th>
                <th>E-Mail</th>
                <th>ID</th>
                <th>Settings</th>
            </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr id="item{{$item->id}}">
                <td class="user-image">
                @if ($item->photo)
                    <img src="{{$item->photo}}" width="40" height="40" class="img-circle" alt="{{$item->firstname}} {{$item->lastname}}" />
                @endif
                </td>
                <td class="user-name">
                    <a href="{{cms_route('cmsUsers.edit', [$item->id])}}" class="name{{AuthCms::id() == $item->id ? ' active' : ''}}">{{$item->firstname}} {{$item->lastname}}</a>
                    <span>{{$item->role_text}}</span>
                </td>
                <td>
                    <span class="email">{{$item->email}}</span>
                </td>
                <td class="user-id">
                    {{$item->id}}
                </td>
                <td class="action-links">
                    <a href="{{cms_route('cmsUsers.show', [$item->id])}}" class="show">
                        <i class="fa fa-user"></i>
                        Profile
                    </a>
                @if (AuthCms::get()->isAdmin() || AuthCms::id() == $item->id)
                    <a href="{{cms_route('cmsUsers.edit', [$item->id])}}" class="edit">
                        <i class="fa fa-pencil"></i>
                        Edit Profile
                    </a>
                @endif
                @if (AuthCms::get()->isAdmin() && $item->role != 'admin')
                    <a href="{{cms_route('permissions.index', [$item->id])}}" class="text-warning">
                        <i class="fa fa-lock"></i>
                        Permissions
                    </a>
                @endif
                @if (AuthCms::get()->isAdmin() && AuthCms::id() != $item->id)
                    <a href="#" class="delete" data-id="{{ $item->id }}" >
                        <i class="fa fa-user-times"></i>
                        Delete
                    </a>
                @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="pull-left">
        <a href="{{ cms_route('cmsUsers.create') }}" class="btn btn-secondary btn-icon-standalone">
            <i class="fa fa-user-plus"></i>
            <span>{{ trans('general.create') }}</span>
        </a>
    </div>
    <div class="pull-right">
        {!! $items->appends(['role' => request('role')])->render() !!}
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('.members-table a.delete').on('click', function(e) {
        e.preventDefault();

        var perform = confirm("{{trans('general.delete_confirm')}}");
        if (perform != true) return;

        var item = $(this);
        var id = $(this).data('id');

        var data = {'id':id, '_method':'delete', '_token':csrf_token()};

        $.post("{{cms_route('cmsUsers.index')}}/" + id, data, function(data) {
            $('body').append(data.view);
            if (data.result) {
                item.closest('tr').remove();
            }
        }, 'json').fail(function(xhr) {
            alert(xhr.responseText);
        });
    });
});
</script>
@endsection
