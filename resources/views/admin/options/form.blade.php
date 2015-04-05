{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group required{{$errors->has('title') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label" for="title{{$lang}}">Title:</label>
    <div class="col-sm-6">
        {!! Form::text('title', null, [
            'id' => 'title' . $lang,
            'class' => 'form-control',
        ]) !!}
        @if ($errors->has('title'))
        <span>{{$errors->first('title')}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <label class="col-sm-2 control-label">Visible:</label>
    <div class="col-sm-10">
        {!! Form::checkbox('visible', null, true, [
            'id' => 'visible' . $lang,
            'class' => 'visible iswitch iswitch-secondary',
            'data-type' => 'general'
        ]) !!}
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <div class="col-sm-10 btn-action pull-right">
        <button type="submit" class="btn btn-secondary" title="{{ $submit }}">
            <i class="fa fa-{{ $icon }}"></i>
        </button>
    @if (! is_null($lang))
        <a href="{{ cms_route($collection->type . '.index', [$collection->id]) }}" class="btn btn-info" title="{{ $collection->title }}">
            <i class="{{icon_type($collection->type)}}"></i>
        </a>
    @endif
        <a href="{{ cms_route('options.index', [$collection->id]) }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>
