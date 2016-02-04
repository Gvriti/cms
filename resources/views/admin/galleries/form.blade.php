{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group{{($error = $errors->first('title')) ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label required">Title:</label>
    <div class="col-sm-10">
        {!! Form::text('title', null, [
            'id' => 'title' . $lang,
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
            'id' => 'short_title' . $lang,
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
            'id' => 'slug' . $lang,
            'class' => 'slug form-control',
            'data-type' => 'general',
        ]) !!}
        @if ($error)
        <span>{{$error}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="form-group{{($error = $errors->first('type')) ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label required">Type:</label>
    <div class="col-sm-10">
        {!! Form::select('type', inner_collection('galleries.types'), null, [
            'id' => 'type' . $lang,
            'class' => 'form-control select',
        ] + ($current->id ? ['disabled' => 'disabled'] : [])) !!}
        @if ($error)
        <span>{{$error}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group{{($error = $errors->first('admin_order_by')) ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label required">Admin order by:</label>
            <div class="col-sm-8">
                {!! Form::select('admin_order_by', inner_collection('galleries.order_by'), null, [
                    'id' => 'admin_order_by',
                    'class' => 'form-control select',
                ]) !!}
                @if ($error)
                <span>{{$error}}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group{{($error = $errors->first('site_order_by')) ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label required">Site order by:</label>
            <div class="col-sm-8">
                {!! Form::select('site_order_by', inner_collection('galleries.order_by'), null, [
                    'id' => 'site_order_by',
                    'class' => 'form-control select',
                ]) !!}
                @if ($error)
                <span>{{$error}}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group{{($error = $errors->first('admin_sort')) ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label required">Admin sort:</label>
            <div class="col-sm-8">
                {!! Form::select('admin_sort', inner_collection('galleries.sort'), null, [
                    'id' => 'admin_sort' . $lang,
                    'class' => 'admin_sort form-control select',
                    'data-type' => 'general'
                ]) !!}
                @if ($error)
                <span>{{$error}}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group{{($error = $errors->first('site_sort')) ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label required">Site sort:</label>
            <div class="col-sm-8">
                {!! Form::select('site_sort', inner_collection('galleries.sort'), null, [
                    'id' => 'site_sort' . $lang,
                    'class' => 'site_sort form-control select',
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

<div class="row">
    <div class="col-sm-6">
        <div class="form-group{{($error = $errors->first('admin_per_page')) ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label required">Admin per page:</label>
            <div class="col-sm-8">
                <div id="admin_per_page{{$lang}}" class="input-group spinner" data-type="general" data-step="1" data-min="2" data-max="50">
                    <div class="input-group-btn">
                        <span class="btn btn-info" data-type="decrement">-</span>
                    </div>
                    {!! Form::text('admin_per_page', null, [
                        'class' => 'admin_per_page form-control text-center',
                        'readonly' => 1,
                    ]) !!}
                    <div class="input-group-btn">
                        <span class="btn btn-info" data-type="increment">+</span>
                    </div>
                </div>
                @if ($error)
                <span class="text-danger">{{$error}}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group{{($error = $errors->first('site_per_page')) ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label required">Site per page:</label>
            <div class="col-sm-8">
                <div id="site_per_page{{$lang}}" class="input-group spinner" data-type="general" data-step="1" data-min="2" data-max="50">
                    <div class="input-group-btn">
                        <span class="btn btn-info" data-type="decrement">-</span>
                    </div>
                    {!! Form::text('site_per_page', null, [
                        'class' => 'site_per_page form-control text-center',
                        'readonly' => 1
                    ]) !!}
                    <div class="input-group-btn">
                        <span class="btn btn-info" data-type="increment">+</span>
                    </div>
                </div>
                @if ($error)
                <span class="text-danger">{{$error}}</span>
                @endif
            </div>
        </div>
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

<div class="form-group{{($error = $errors->first('meta_desc')) ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Meta description:</label>
    <div class="col-sm-10">
        {!! Form::text('meta_desc', null, [
            'id' => 'meta_desc' . $lang,
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
    <div class="col-sm-6">
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
    @if ($current->id)
        <a href="{{ cms_route($current->type . '.index', [$current->id]) }}" class="btn btn-info" title="{{ trans('general.'.$current->type) }}">
            <span class="{{icon_type($current->type)}}"></span>
        </a>
    @endif
        <a href="{{ cms_route('galleries.index', [$current->collection_id]) }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>
