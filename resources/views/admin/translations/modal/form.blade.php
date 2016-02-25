<div class="modal-body">
    <div class="form-group">
        <label class="col-sm-2 control-label required">Name:</label>
        <div class="col-sm-10">
            {!! Form::text('name', null, [
                'id' => 'name' . $current->language,
                'class' => 'form-control',
                'data-type' => 'general'
            ] + ($current->name ? ['readonly'] : [])) !!}
            @if ($error = $errors->first('name'))
            <div class="text-danger">{{$error}}</div>
            @endif
            <span class="description">Name is the identifier for the "value"</span>
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label required">Title:</label>
        <div class="col-sm-10">
            {!! Form::text('title', null, [
                'id' => 'title' . $current->language,
                'class' => 'form-control',
                'data-type' => 'general'
            ]) !!}
            @if ($error = $errors->first('title'))
            <div class="text-danger">{{$error}}</div>
            @endif
            <span class="description">Title for the "value". It's visible only for CMS Users</span>
        </div>
    </div>

    <div class="form-group-separator"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label required">Value:</label>
        <div class="col-sm-10">
            {!! Form::text('value', null, [
                'id' => 'value' . $current->language,
                'class' => 'form-control',
            ]) !!}
            @if ($error = $errors->first('value'))
            <div class="text-danger">{{$error}}</div>
            @endif
            <span class="description">Value contains the translated text that will be displayed on the site</span>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general.close') }}</button>
    <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
</div>