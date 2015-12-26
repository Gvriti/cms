{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group required{{$errors->has('title') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Title:</label>
    <div class="col-sm-10">
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

<div class="form-group{{$errors->has('short_title') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Short title:</label>
    <div class="col-sm-10">
        {!! Form::text('short_title', null, [
            'id' => 'short_title' . $lang,
            'class' => 'form-control',
        ]) !!}
        @if ($errors->has('short_title'))
        <span>{{$errors->first('short_title')}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group{{$errors->has('slug') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Slug:</label>
    <div class="col-sm-10">
        {!! Form::text('slug', null, [
            'id' => 'slug' . $lang,
            'class' => 'slug form-control',
            'data-type' => 'general',
        ]) !!}
        @if ($errors->has('slug'))
        <span>{{$errors->first('slug')}}</span>
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
    <label class="col-sm-2 control-label">Description:</label>
    <div class="col-sm-10">
        {!! Form::textarea('description', null, [
            'id' => 'description' . $lang,
            'class' => 'form-control text-editor',
            'rows' => '5'
        ]) !!}
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <label class="col-sm-2 control-label">Content:</label>
    <div class="col-sm-10">
        {!! Form::textarea('content', null, [
            'id' => 'content' . $lang,
            'class' => 'form-control text-editor',
            'rows' => '10'
        ]) !!}
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group{{$errors->has('meta_desc') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Meta description:</label>
    <div class="col-sm-10">
        {!! Form::text('meta_desc', null, [
            'id' => 'meta_desc' . $lang,
            'class' => 'form-control',
        ]) !!}
        @if ($errors->has('meta_desc'))
        <span>{{$errors->first('meta_desc')}}</span>
        @endif
        <span class="description">Description for search engines. It is best to keep meta descriptions less then 150 or 160 characters.</span>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <label class="col-sm-2 control-label">Image:</label>
    <div class="col-lg-6 col-sm-10">
        <div class="input-group">
            {!! Form::text('image', null, [
                'id' => 'image' . $lang,
                'class' => 'image form-control',
                'data-type' => 'general'
            ]) !!}
            <div class="input-group-btn popup" data-browse="image">
                <span class="btn btn-info">არჩევა</span>
            </div>
        </div>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <div class="col-sm-10 btn-action pull-right">
        <button type="submit" class="btn btn-secondary" title="{{ $submit }}">
            <i class="fa fa-{{ $icon }}"></i>
        </button>
        <a href="{{ cms_route('articles.index', [$collectionId]) }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>
