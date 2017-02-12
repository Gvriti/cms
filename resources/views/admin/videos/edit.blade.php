@if (! empty($items))
<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="tab-content">
            @foreach ($items as $item)
                <div class="tab-pane{{language() != $item->language ? '' : ' active'}}" id="modal-item-{{$item->language}}">
                    <div class="modal-gallery-image embed-responsive embed-responsive-16by9">
                        <iframe src="{{get_youtube_embed($item->file)}}" frameborder="0" allowfullscreen class="embed-responsive-item"></iframe>
                    </div>
                    {!! Form::model($item, [
                        'method' => 'put',
                        'url'    => cms_route('videos.update', [$item->gallery_id, $item->id], is_multilanguage() ? $item->language : null),
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
                                            'class' => 'form-control',
                                            'autofocus'
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Video Link:</label>
                                        {!! Form::text('file', null, [
                                            'id' => 'file' . $item->language,
                                            'class' => 'form-control',
                                            'data-lang' => 1
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Visible:</label>
                                        {!! Form::checkbox('visible', null, null, [
                                            'id' => 'visible' . $item->language,
                                            'class' => 'iswitch iswitch-secondary',
                                            'data-lang' => 1
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
        @if (is_multilanguage())
            <ul class="modal-footer modal-gallery-top-controls nav nav-tabs">
            @foreach ($items as $item)
                <li{!!language() != $item->language ? '' : ' class="active"'!!}>
                    <a href="#modal-item-{{$item->language}}" data-toggle="tab">
                        <span class="visible-xs">{{$item->language}}</span>
                        <span class="hidden-xs">{{language($item->language)}}</span>
                    </a>
                </li>
            @endforeach
            </ul>
        @endif
        </div>
    </div>
</div>
@push('scripts.bottom')
<script type="text/javascript">
    var currentLang = '{{language()}}';
    var formSelector = '#form-modal .ajax-form';
    $(formSelector).on('ajaxFormSuccess', function(e, data) {
        var lang = $(this).data('lang');
        if (lang == currentLang) {
            var item = $(formSelector + '[data-lang="'+lang+'"]');

            var title   = $('[name="title"]', item).val();
            var file    = $('[name="file"]', item).val();
            var visible = $('[name="visible"]', item).prop('checked');

            var item = $('.gallery-env #item{{$item->id}}');
            $('.title', item).text(title);
            $('.thumb iframe', item).attr('src', data.youtube);
            $('#form-modal iframe').attr('src', data.youtube);

            var icon = visible ? 'fa-eye' : 'fa-eye-slash'
            $('.visibility i', item).attr('class', icon);
        }
    });
</script>
@endpush
@endif