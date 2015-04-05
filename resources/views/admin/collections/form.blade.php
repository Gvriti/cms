@if ($type_disabled)
{!! Form::hidden('type', null) !!}
@endif
{!! Form::hidden('close', false, ['class' => 'form-close']) !!}
<div class="form-group required{{$errors->has('title') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Title:</label>
    <div class="col-sm-10">
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

<div class="form-group required{{$errors->has('type') ? ' validate-has-error' : '' }}">
    <label class="col-sm-2 control-label">Type:</label>
    <div class="col-sm-10">
        {!! Form::select('type', collection_types(), null, [
            'id' => 'type',
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
                {!! Form::select('admin_order_by', collection_order(), null, [
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
                {!! Form::select('site_order_by', collection_order(), null, [
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
                {!! Form::select('admin_sort', collection_sorts(), null, [
                    'id' => 'admin_sort',
                    'class' => 'form-control select',
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
                {!! Form::select('site_sort', collection_sorts(), null, [
                    'id' => 'site_sort',
                    'class' => 'form-control select',
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
                <div id="admin_per_page" class="input-group spinner" data-step="1" data-min="1" data-max="50">
                    <div class="input-group-btn">
                        <span class="btn btn-info" data-type="decrement">-</span>
                    </div>
                    {!! Form::text('admin_per_page', null, [
                        'class' => 'form-control text-center',
                        'readonly' => 1
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
                <div id="site_per_page" class="input-group spinner" data-step="1" data-min="1" data-max="50">
                    <div class="input-group-btn">
                        <span class="btn btn-info" data-type="decrement">-</span>
                    </div>
                    {!! Form::text('site_per_page', null, [
                        'class' => 'form-control text-center',
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
    <label class="col-sm-2 control-label">Description:</label>

    <div class="col-sm-10">
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Short description']) !!}
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
        <a href="{{ cms_route('collections.index') }}" class="btn btn-blue" title="{{ trans('general.back') }}">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('.select').select2({
        placeholder: 'Select type...',
        allowClear: true
    }).on('select2-open', function() {
        // Adding Custom Scrollbar
        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
    });
});
</script>

<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
