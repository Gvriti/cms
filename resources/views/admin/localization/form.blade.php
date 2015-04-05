{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group required{{$errors->has('name') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label" for="name{{$lang}}">Name:</label>
    <div class="col-sm-6">
        {!! Form::text('name', null, [
            'id' => 'name' . $lang,
            'class' => 'name form-control',
            'data-type' => 'general'
        ]) !!}
        @if ($errors->has('name'))
        <span>{{$errors->first('name')}}</span>
        @endif
        <span class="description">name is the identifier for the value (it's not changeable after creation!)</span>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group required{{$errors->has('title') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label" for="title{{$lang}}">Title:</label>
    <div class="col-sm-6">
        {!! Form::text('title', null, [
            'id' => 'title' . $lang,
            'class' => 'title form-control',
            'data-type' => 'general'
        ]) !!}
        @if ($errors->has('title'))
        <span>{{$errors->first('title')}}</span>
        @endif
        <span class="description">Title is a short description of the "value", visible only for CMS Users</span>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group required{{$errors->has('value') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label" for="value{{$lang}}">Value:</label>
    <div class="col-sm-6">
        {!! Form::text('value', null, [
            'id' => 'value' . $lang,
            'class' => 'form-control',
        ]) !!}
        @if ($errors->has('value'))
        <span>{{$errors->first('value')}}</span>
        @endif
        <span class="description">Value contains the translated text that will be displayed on the site</span>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <div class="col-sm-10 btn-action pull-right">
        <button type="submit" class="btn btn-secondary" title="{{ $submit }}">
            <i class="fa fa-{{ $icon }}"></i>
        </button>
        <a href="{{ cms_route('localization.index') }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>