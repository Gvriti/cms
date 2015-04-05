<div class="member-form-add-header">
    <div class="row">
        <div class="col-md-2 col-sm-4 pull-right-sm">
            <div class="action-buttons">
            @if ($item->id)
                <div class="profile">
                    <a href="{{cms_route('cmsUsers.show', [$item->id])}}" class="btn btn-block btn-turquoise">{{trans('general.profile')}}</a>
                </div>
            @if (AuthCms::get()->isAdmin())
                <div class="permissions{{$item->role == 'admin' ? ' hidden' : ''}}">
                    <a href="{{cms_route('permissions.index', [$item->id])}}" class="btn btn-block btn-orange">Permissions</a>
                </div>
            @endif
            @endif
            </div>
        </div>
        <div class="col-md-10 col-sm-8">
            <div class="user-img">
                <img src="{{$item->photo}}" width="128" class="img-circle" alt="Photo" />
            </div>
        @if ($item->id)
            <div class="user-name">
                <a href="{{cms_route('cmsUsers.show', [$item->id])}}">{{$item->firstname}} {{$item->lastname}}</a>
                <span>{{$item->role_text}}</span>
            </div>
        @endif
        </div>
    </div>
</div>
<div class="member-form-inputs">
    <div class="form-group required{{$errors->has('email') ? ' validate-has-error' : '' }}">
        <label class="col-sm-2 control-label text-left" for="email">Email:</label>
        <div class="col-sm-10">
            {!! Form::text('email', null, [
                'id' => 'email',
                'class' => 'form-control',
            ]) !!}
            @if ($errors->has('email'))
            <span>{{$errors->first('email')}}</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group required{{$errors->has('firstname') ? ' validate-has-error' : '' }}">
                <label class="col-sm-4 control-label text-left" for="firstname">Firstname:</label>
                <div class="col-sm-8">
                    {!! Form::text('firstname', null, [
                        'id' => 'firstname',
                        'class' => 'form-control',
                    ]) !!}
                    @if ($errors->has('firstname'))
                    <span>{{$errors->first('firstname')}}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group required{{$errors->has('lastname') ? ' validate-has-error' : '' }}">
                <label class="col-sm-4 control-label text-left" for="lastname">Lastname:</label>
                <div class="col-sm-8">
                    {!! Form::text('lastname', null, [
                        'id' => 'lastname',
                        'class' => 'form-control',
                    ]) !!}
                    @if ($errors->has('lastname'))
                    <span>{{$errors->first('lastname')}}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div class="form-group{{$errors->has('phone') ? ' validate-has-error' : '' }}">
        <label class="col-sm-2 control-label text-left" for="phone">Phone:</label>
        <div class="col-sm-10">
            {!! Form::text('phone', null, [
                'id' => 'phone',
                'class' => 'form-control',
            ]) !!}
            @if ($errors->has('phone'))
            <span>{{$errors->first('phone')}}</span>
            @endif
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label text-left" for="address">Address:</label>
        <div class="col-sm-10">
            {!! Form::text('address', null, [
                'id' => 'address',
                'class' => 'form-control',
            ]) !!}
        </div>
    </div>

@if (AuthCms::get()->isAdmin() && AuthCms::id() != $item->id)
    <div class="form-group-separator"></div>

    <div class="form-group required{{$errors->has('role') ? ' validate-has-error' : '' }}">
        <label class="col-sm-2 control-label text-left">Role:</label>
        <div class="col-sm-10">
            {!! Form::select('role', $roles, null, [
                'id' => 'role',
                'class' => 'form-control',
            ]) !!}
            @if ($errors->has('role'))
            <span>{{$errors->first('role')}}</span>
            @endif
        </div>
    </div>
@else
    {!! Form::hidden('role', null) !!}
@endif

    <div class="form-group-separator"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label text-left">Active:</label>
        <div class="col-sm-10">
            {!! Form::checkbox('active', null, true, [
                'id' => 'active',
                'class' => 'iswitch iswitch-secondary'
            ]) !!}
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label text-left" for="photo">Photo:</label>
        <div class="col-sm-6">
            <div class="input-group">
                {!! Form::text('photo', null, [
                    'id' => 'photo',
                    'class' => 'form-control'
                ]) !!}
                <div class="input-group-btn popup" data-browse="photo">
                    <span class="btn btn-info">არჩევა</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div id="change-password" class="form-group{{ ! $item->id ? '' : ' collapse' . ($errors->has('password') ? ' in' : '')}}">
        <div class="col-sm-6">
            <div class="form-group{{$errors->has('password') ? ' validate-has-error' : '' }}">
                <label class="col-sm-4 control-label text-left" for="password">Password:</label>
                <div class="col-sm-8">
                    {!! Form::password('password', [
                        'id' => 'password',
                        'class' => 'form-control'
                    ]) !!}
                    @if ($errors->has('password'))
                    <span>{{$errors->first('password')}}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="col-sm-4 control-label text-left" for="password_confirmation">Repeat Password:</label>
                <div class="col-sm-8">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-secondary">{{$submit}}</button>
    <a href="{{ cms_route('cmsUsers.index') }}" class="btn btn-blue">{{ trans('general.back') }}</a>
@if ($item->id)
    <div class="btn btn-info pull-right" data-toggle="collapse" data-target="#change-password">პაროლის შეცვლა</div>
@endif
</div>
@if ($item->id)
<script type="text/javascript">
$(function() {
    $('.ajax-form').on('ajaxFormSuccess', function() {
        var firstname = $('#firstname', this).val();
        var lastname = $('#lastname', this).val();
        var photo = $('#photo', this).val();
        var role = $('[name="role"]', this).val();
        var roles = ['{!!implode("', '", $roles)!!}'];

        $('.user-name a', this).text(firstname + ' ' + lastname);
        $('.user-name span', this).text(roles[role]);
        $('.user-img img', this).attr('src', photo);
        $('.user-img img', this).attr('src', photo);
        if (role > 0) {
            $('.permissions', this).removeClass('hidden');
        } else {
            $('.permissions', this).addClass('hidden');
        }

        $('#password, #password_confirmation', this).val('');
    });
});
</script>
@endif