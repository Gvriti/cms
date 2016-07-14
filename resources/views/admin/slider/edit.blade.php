@if (! empty($items))
<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="tab-content">
            @foreach ($items as $item)
                <div class="tab-pane{{language() != $item->language ? '' : ' active'}}" id="modal-item-{{$item->language}}">
                    <div class="modal-gallery-image">
                        <img src="{{$item->file ?: $item->file_default}}" class="file{{$item->language}} img-responsive" />
                    </div>
                    {!! Form::model($item, [
                        'method' => 'put',
                        'url'    => cms_route('slider.update', [$item->id], ($isMultiLang = (count(languages()) > 1)) ? $item->language : null),
                        'class'  => 'form-horizontal '.$settings->get('ajax_form'),
                        'data-lang' => $item->language
                    ]) !!}
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Title:</label>
                                        {!! Form::text('title', null, [
                                            'id' => 'title' . $item->language,
                                            'class' => 'title form-control',
                                            'autofocus'
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Short description:</label>
                                        {!! Form::textarea('description', null, [
                                            'id' => 'description' . $item->language,
                                            'class' => 'form-control',
                                            'rows' => '2'
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Image:</label>
                                        <div class="input-group">
                                            {!! Form::text('file', null, [
                                                'id' => 'file' . $item->language,
                                                'class' => 'file form-control',
                                                'data-type'  => 'general',
                                            ]) !!}
                                            <div class="input-group-btn popup" data-browse="file{{$item->language}}">
                                                <span class="btn btn-info">არჩევა</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Visible:</label>
                                        {!! Form::checkbox('visible', null, null, [
                                            'id' => 'visible' . $item->language,
                                            'class' => 'iswitch iswitch-secondary',
                                            'data-type' => 'general'
                                        ]) !!}
                                    </div>
                                </div>
                                <button type="button" class="btn btn-md btn-white" data-dismiss="modal">{{trans('general.close')}}</button>
                                <button type="submit" class="btn btn-md btn-secondary">{{trans('general.save')}}</button>
                            </div>
                        </div>
                    {!!Form::close()!!}
                </div>
            @endforeach
            </div>
        @if ($isMultiLang)
            <ul class="modal-footer modal-gallery-top-controls nav nav-tabs">
            @foreach ($items as $item)
                <li{!!language() != $item->language ? '' : ' class="active"'!!}>
                    <a href="#modal-item-{{$item->language}}" data-toggle="tab">
                        <span class="visible-xs">{{$item->language}}</span>
                        <span class="hidden-xs">{{languages($item->language)}}</span>
                    </a>
                </li>
            @endforeach
            </ul>
        @endif
        </div>
    </div>
</div>
<script type="text/javascript">
    var currentLang = '{{language()}}';
    var formSelector = '#form-modal .ajax-form';
    $(formSelector).on('ajaxFormSuccess', function(e) {
        var lang = $(this).data('lang');
        if (lang == currentLang) {
            var item = $(formSelector + '[data-lang="'+lang+'"]');

            var title   = $('.title', item).val();
            var file    = $('.file', item).val();
            var visible = $('.visible', item).prop('checked');

            var item = $('.gallery-env #item{{$item->id}}');
            $('.title', item).text(title);
            $('.thumb img', item).attr('src', file);

            var icon = visible ? 'fa-eye' : 'fa-eye-slash'
            $('.visibility i', item).attr('class', icon);
        }
    });

    $(formSelector + ' .file').on('fileSet', function(e) {
        var fileId    = $(this).attr('id');
        var fileValue = $(this).val();
        $('#form-modal .' + fileId).attr('src', fileValue);
    });
</script>
@endif