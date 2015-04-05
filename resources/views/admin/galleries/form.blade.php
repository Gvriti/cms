@if ($type_disabled)
{!! Form::hidden('type', null) !!}
@endif
{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group required{{$errors->has('title') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label" for="title{{$lang}}">Title:</label>
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
    <label class="col-sm-2 control-label" for="short_title{{$lang}}">Short title:</label>
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
    <label class="col-sm-2 control-label" for="slug{{$lang}}">Slug:</label>
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

<div class="form-group required{{$errors->has('type') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Type:</label>
    <div class="col-sm-10">
        {!! Form::select('type', gallery_types(), null, [
            'id' => 'type' . $lang,
            'class' => 'form-control select',
        ] + $type_disabled) !!}
        @if ($errors->has('type'))
        <span>{{$errors->first('type')}}</span>
        @endif
    </div>
</div>

<div class="form-group-separator"></div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group required{{$errors->has('admin_order_by') ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label">Admin order by:</label>
            <div class="col-sm-8">
                {!! Form::select('admin_order_by', gallery_order(), null, [
                    'id' => 'admin_order_by',
                    'class' => 'form-control select',
                ]) !!}
                @if ($errors->has('admin_order_by'))
                <span>{{$errors->first('admin_order_by')}}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group required{{$errors->has('site_order_by') ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label">Site order by:</label>
            <div class="col-sm-8">
                {!! Form::select('site_order_by', gallery_order(), null, [
                    'id' => 'site_order_by',
                    'class' => 'form-control select',
                ]) !!}
                @if ($errors->has('site_order_by'))
                <span>{{$errors->first('site_order_by')}}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group required{{$errors->has('admin_sort') ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label">Admin sort:</label>
            <div class="col-sm-8">
                {!! Form::select('admin_sort', gallery_sorts(), null, [
                    'id' => 'admin_sort' . $lang,
                    'class' => 'admin_sort form-control select',
                    'data-type' => 'general'
                ]) !!}
                @if ($errors->has('admin_sort'))
                <span>{{$errors->first('admin_sort')}}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group required{{$errors->has('site_sort') ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label">Site sort:</label>
            <div class="col-sm-8">
                {!! Form::select('site_sort', gallery_sorts(), null, [
                    'id' => 'site_sort' . $lang,
                    'class' => 'site_sort form-control select',
                    'data-type' => 'general'
                ]) !!}
                @if ($errors->has('site_sort'))
                <span>{{$errors->first('site_sort')}}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="form-group-separator"></div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group required{{$errors->has('admin_per_page') ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label">Admin per page:</label>
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
                @if ($errors->has('admin_per_page'))
                <span class="text-danger">{{$errors->first('admin_per_page')}}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group required{{$errors->has('site_per_page') ? ' validate-has-error' : '' }}">
            <label class="col-sm-4 control-label">Site per page:</label>
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
                @if ($errors->has('site_per_page'))
                <span class="text-danger">{{$errors->first('site_per_page')}}</span>
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

<div class="form-group{{$errors->has('meta_desc') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label" for="meta_desc{{$lang}}">Meta description:</label>
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
    <label class="col-sm-2 control-label" for="image{{$lang}}">Image:</label>
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
    @if ($type_disabled)
        <a href="{{ cms_route($item->type . '.index', [$item->id]) }}" class="btn btn-info" title="{{ trans('general.'.$item['type']) }}">
            <span class="{{icon_type($item['type'])}}"></span>
        </a>
    @endif
        <a href="{{ cms_route('galleries.index', [$item['collection_id']]) }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>
