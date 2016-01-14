<div class="member-form-inputs">
    <div class="form-group required{{($error = $errors->first('email')) ? ' validate-has-error' : '' }}">
        <label class="col-sm-2 control-label text-left">Email:</label>
        <div class="col-sm-10">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('email', null, [
                    'id' => 'email',
                    'class' => 'form-control',
                ]) !!}
            </div>
            @if ($error)
            <span class="text-danger">{{$error}}</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group required{{($error = $errors->first('firstname')) ? ' validate-has-error' : '' }}">
                <label class="col-sm-4 control-label text-left">Firstname:</label>
                <div class="col-sm-8">
                    {!! Form::text('firstname', null, [
                        'id' => 'firstname',
                        'class' => 'form-control',
                    ]) !!}
                    @if ($error)
                    <span>{{$error}}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group required{{($error = $errors->first('lastname')) ? ' validate-has-error' : '' }}">
                <label class="col-sm-4 control-label text-left">Lastname:</label>
                <div class="col-sm-8">
                    {!! Form::text('lastname', null, [
                        'id' => 'lastname',
                        'class' => 'form-control',
                    ]) !!}
                    @if ($error)
                    <span>{{$error}}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div class="form-group{{($error = $errors->first('phone')) ? ' validate-has-error' : '' }}">
        <label class="col-sm-2 control-label text-left">Phone:</label>
        <div class="col-sm-10">
            {!! Form::text('phone', null, [
                'id' => 'phone',
                'class' => 'form-control',
            ]) !!}
            @if ($error)
            <span>{{$error}}</span>
            @endif
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label text-left">Address:</label>
        <div class="col-sm-10">
            {!! Form::text('address', null, [
                'id' => 'address',
                'class' => 'form-control',
            ]) !!}
        </div>
    </div>

@if (AuthCms::get()->isAdmin() && AuthCms::id() != $item->id)
    <div class="form-group-separator"></div>

    <div class="form-group required{{($error = $errors->first('role')) ? ' validate-has-error' : '' }}">
        <label class="col-sm-2 control-label text-left">Role:</label>
        <div class="col-sm-10">
            {!! Form::select('role', $roles, null, [
                'id' => 'role',
                'class' => 'form-control',
            ]) !!}
            @if ($error)
            <span>{{$error}}</span>
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
        <label class="col-sm-2 control-label text-left">Photo:</label>
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
            <div class="form-group{{($error = $errors->first('password')) ? ' validate-has-error' : '' }}">
                <label class="col-sm-4 control-label text-left">Password:</label>
                <div class="col-sm-8">
                    {!! Form::password('password', [
                        'id' => 'password',
                        'class' => 'form-control'
                    ]) !!}
                    @if ($error)
                    <span>{{$error}}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="col-sm-4 control-label text-left">Repeat Password:</label>
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