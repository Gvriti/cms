{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group required{{$errors->has('title') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Title:</label>
    <div class="col-lg-6 col-sm-10">
        {!! Form::text('title', null, [
            'id' => 'title',
            'class' => 'form-control',
        ]) !!}
        @if ($errors->has('title'))
        <span>{{$errors->first('title')}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <label class="col-sm-2 control-label">Description:</label>

    <div class="col-lg-6 col-sm-10">
        {!! Form::textarea('description', null, [
            'class' => 'form-control',
            'rows' => '3'
        ]) !!}
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <div class="col-sm-10 btn-action pull-right">
        <button type="submit" class="btn btn-secondary" title="{{ $submit }}">
            <i class="fa fa-{{ $icon }}"></i>
        </button>
    @if ($id)
        <a href="{{ cms_route('pages.index', [$id]) }}" class="btn btn-info" title="{{ trans('general.pages') }}">
            <i class="{{icon_type('pages')}}"></i>
        </a>
    @endif
        <a href="{{ cms_route('menus.index') }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>
