{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group{{($error = $errors->first('title')) ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label required">Title:</label>
    <div class="col-sm-10">
        {!! Form::text('title', null, [
            'id' => 'title' . $current->language,
            'class' => 'form-control',
        ]) !!}
        @if ($error)
        <span>{{$error}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group{{($error = $errors->first('short_title')) ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Short title:</label>
    <div class="col-sm-10">
        {!! Form::text('short_title', null, [
            'id' => 'short_title' . $current->language,
            'class' => 'form-control',
        ]) !!}
        @if ($error)
        <span>{{$error}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group{{($error = $errors->first('slug')) ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Slug:</label>
    <div class="col-sm-10">
        {!! Form::text('slug', null, [
            'id' => 'slug' . $current->language,
            'class' => 'form-control',
            'data-type' => 'general'
        ]) !!}
        @if ($error)
        <span>{{$error}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="row">
    <div class="col-lg-4">
        <div class="form-group{{($error = $errors->first('type')) ? ' validate-has-error' : '' }}">
            <label class="col-lg-6 col-sm-2 control-label required">Type:</label>
            <div class="col-lg-6 col-sm-10">
                {!! Form::select('type', $types, null, [
                    'id' => 'type' . $current->language,
                    'class' => 'form-control select',
                    'data-type' => 'general'
                ]) !!}
                @if ($error)
                <span>{{$error}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8 template{{($templates = cms_pages('templates.' . $current->type, [])) ? '' : ' hidden'}}">
        <div class="form-group">
            <label class="col-lg-4 col-sm-2 control-label">Template:</label>
            <div class="col-lg-6 col-sm-10">
                {!! Form::select('template', ['' => ''] + $templates, null, [
                    'id' => 'template' . $current->language,
                    'class' => 'form-control select',
                    'data-type' => 'general'
                ]) !!}
            </div>
        </div>
    </div>
    <div class="col-lg-8 collection{{$current->collection_id || $errors->has('collection_id') ? '' : ' hidden'}}">
        <div class="form-group{{($error = $errors->first('collection_id')) ? ' validate-has-error' : '' }}">
            <label class="col-lg-4 col-sm-2 control-label required">Collection:</label>
            <div class="col-lg-6 col-sm-10">
                {!! Form::select('collection_id', ['' => ''] + $collections, null, [
                    'id' => 'collection_id' . $current->language,
                    'class' => 'form-control select',
                    'data-type' => 'general'
                ]) !!}
                @if ($error)
                <span>{{$error}}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group">
    <label class="col-sm-2 control-label">Description:</label>
    <div class="col-sm-10">
        {!! Form::textarea('description', null, [
            'id' => 'description' . $current->language,
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
            'id' => 'content' . $current->language,
            'class' => 'form-control text-editor',
            'rows' => '10'
        ]) !!}
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group{{($error = $errors->first('meta_desc')) ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Meta description:</label>
    <div class="col-sm-10">
        {!! Form::text('meta_desc', null, [
            'id' => 'meta_desc' . $current->language,
            'class' => 'form-control',
        ]) !!}
        @if ($error)
        <span>{{$error}}</span>
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
                'id' => 'image' . $current->language,
                'class' => 'form-control',
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
    <label class="col-sm-2 control-label">Visible:</label>
    <div class="col-sm-10">
        {!! Form::checkbox('visible', null, null, [
            'id' => 'visible' . $current->language,
            'class' => 'iswitch iswitch-secondary',
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
        <a href="{{ cms_route('pages.index', [$current->menu_id]) }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>
