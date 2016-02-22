@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{$icon = icon_type('cms_users')}}"></i>
            CMS Users
        </h1>
        <p class="description">Edit CMS user</p>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li class="active">
                <i class="{{$icon}}"></i>
                <strong>CMS Users</strong>
            </li>
        </ol>
    </div>
</div>
<div class="panel panel-headerless">
    <div class="panel-body">
        {!! Form::model($current, [
            'method' => 'put',
            'url'    => cms_route('cmsUsers.update', [$current->id]),
            'class'  => 'form-horizontal '.$settings->get('ajax_form')
        ]) !!}
            <div class="member-form-add-header">
                <div class="row">
                    <div class="col-md-2 col-sm-4 pull-right-sm">
                        <div class="action-buttons">
                            <div class="profile">
                                <a href="{{$routeShow = cms_route('cmsUsers.show', [$current->id])}}" class="btn btn-block btn-turquoise">{{trans('general.profile')}}</a>
                            </div>
                        @if (AuthCms::get()->isAdmin())
                            <div class="permissions{{$current->role == 'admin' ? ' hidden' : ''}}">
                                <a href="{{cms_route('permissions.index', [$current->id])}}" class="btn btn-block btn-orange">Permissions</a>
                            </div>
                        @endif
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8">
                        <div class="user-img">
                            <img src="{{$current->photo}}" width="128" class="img-circle" alt="Photo" />
                        </div>
                        <div class="user-name">
                            <a href="{{$routeShow}}">{{$current->firstname}} {{$current->lastname}}</a>
                            <span>{{$current->role_text}}</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.cms_users.form', [
                'submit'        => trans('general.update'),
                'submitAndBack' => trans('general.update_n_back'),
                'icon'          => 'save'
            ])
        {!! Form::close() !!}
    </div>
</div>
@endsection
