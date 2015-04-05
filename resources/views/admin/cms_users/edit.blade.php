@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="fa fa-user-secret"></i>
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
                <i class="fa fa-user-secret"></i>
                <strong>CMS Users</strong>
            </li>
        </ol>
    </div>
</div>
<div class="panel panel-headerless">
    <div class="panel-body">
        {!! Form::model($item, [
            'method' => 'put',
            'url'    => cms_route('cmsUsers.update', [$item->id]),
            'class'  => 'form-horizontal '.$settings->get('ajax_form')
        ]) !!}
            @include('admin.cms_users.form', [
                'submit'        => trans('general.update'),
                'submitAndBack' => trans('general.update_n_back'),
                'icon'          => 'save'
            ])
        {!! Form::close() !!}
    </div>
</div>
@endsection
