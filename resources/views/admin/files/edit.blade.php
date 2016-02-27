@if (! empty($items))
<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="tab-content">
            @foreach ($items as $item)
                <div class="tab-pane{{language() != $item->language ? '' : ' active'}}" id="modal-item-{{$item->language}}">
                    <div class="modal-gallery-image">
                    @if (in_array($ext = pathinfo($item->file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{$item->file}}" class="file{{$item->language}} img-responsive" />
                    @elseif( ! empty($ext))
                        <img src="{{asset('assets/images/file-ext-icons/'.$ext.'.png')}}" class="file{{$item->language}} not-photo img-responsive" alt="{{$item->title}}" />
                    @else
                        <img src="{{asset('assets/images/file-ext-icons/www.png')}}" class="file{{$item->language}} not-photo img-responsive" alt="{{$item->title}}" />
                    @endif
                    </div>
                    {!! Form::model($item, [
                        'method' => 'put',
                        'url'    => cms_route('files.update', [$item->route_name, $item->route_id, $item->id], ($isMultiLang = count(languages()) > 1) ? $item->language : null),
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
                                        <label class="control-label">File:</label>
                                        <div class="input-group">
                                            {!! Form::text('file', null, [
                                                'id' => 'file' . $item->language,
                                                'class' => 'form-control',
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
    <script type="text/javascript">
        var currentLang = '{{language()}}';
        var formSelector = '#form-modal .ajax-form';
        $(formSelector).on('ajaxFormSuccess', function(e) {
            var lang = $(this).data('lang');
            if (lang == currentLang) {
                var item = $(formSelector + '[data-lang="'+lang+'"]');

                var title   = $('[name="title"]', item).val();
                var file    = $('[name="file"]', item).val();
                var visible = $('[name="visible"]', item).prop('checked');

                var item = $('.gallery-env #item{{$item->id}}');
                $('[name="title"]', item).text(title);
                $('.thumb img', item).attr('src', getFileImage(file).file);

                var icon = visible ? 'fa-eye' : 'fa-eye-slash'
                $('.visibility i', item).attr('class', icon);
            }
        });

        $(formSelector + ' [name="file"]').on('fileSet', function(e) {
            var fileId    = $(this).attr('id');
            var fileValue = $(this).val();
            var result = getFileImage(fileValue);

            $('#form-modal .' + fileId).removeClass('not-photo');
            if (! result.isPhoto) {
                $('#form-modal .' + fileId).addClass('not-photo');
            }
            $('#form-modal .' + fileId).attr('src', result.file);
        });

        function getFileImage(file) {
            var fileExt = file.substr((~-file.lastIndexOf(".") >>> 0) + 2);
            var result = {'file':file, 'isPhoto':true};
            if (fileExt.length && ['jpg', 'jpeg', 'png', 'gif'].indexOf(fileExt) < 0) {
                file = '{{asset('assets/images/file-ext-icons')}}/' + fileExt + '.png';
                result.isPhoto = false;
            } else if (! fileExt.length) {
                file = '{{asset('assets/images/file-ext-icons/www.png')}}';
                result.isPhoto = false;
            }
            result.file = file;

            return result;
        }
    </script>
</div>
@endif