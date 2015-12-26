@if (! empty($item))
<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-gallery-image">
                <img src="{{$item->file ?: $item->file_default}}" class="img-responsive" />
            </div>
            {!! Form::model($item, [
                'url'   => cms_route('files.store', [$item->route_name, $item->route_id]),
                'class' => 'form-create form-horizontal'
            ]) !!}
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Title:</label>
                        {!! Form::text('title', null, [
                            'id' => 'title',
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">File:</label>
                        <div class="input-group">
                            {!! Form::text('file', null, [
                                'id' => 'file',
                                'class' => 'form-control',
                            ]) !!}
                            <div class="input-group-btn popup" data-browse="file">
                                <span class="btn btn-info">არჩევა</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Visible:</label>
                        {!! Form::checkbox('visible', null, true, [
                            'id' => 'visible',
                            'class' => 'visible iswitch iswitch-secondary'
                        ]) !!}
                    </div>
                </div>
                <button type="button" class="btn btn-md btn-white" data-dismiss="modal">{{trans('general.close')}}</button>
                <button type="submit" class="btn btn-md btn-secondary">{{trans('general.save')}}</button>
            {!!Form::close()!!}
        </div>
    </div>
</div>
<script type="text/javascript">
    var sort = '{{request('sort', 'desc')}}';
    var currentPage = '{{request('page', 1)}}';
    var creationPage = sort == 'desc' ? 1 : {{request('lastPage', 1)}};
    var formSelector = '#form-modal .form-create';

    $(formSelector).on('submit', function(e) {
        e.preventDefault();

        form = $(this);
        $('.form-group', form).find('.text-danger').remove();
        var url = form.attr('action');
        var data = form.serialize();
        $.post(url, data, function(data) {
            var imageContainer = '.gallery-env .album-images';
            var insert = $(imageContainer).data('insert');
            insert = Function("$('"+imageContainer+"')."+insert+"('"+data.view+"');");
            insert();

            cbr_replace();

            if (currentPage != creationPage) {
                window.location.href = '{{cms_route('files.index', [$item->route_name, $item->route_id])}}?page=' + creationPage;
            } else {
                $('#form-modal [data-dismiss]').trigger('click');
            }
        }, 'json').fail(function(xhr) {
            if (xhr.status == 422) {
                var data = xhr.responseJSON;

                $.each(data, function(index, element) {
                    var input = $('#' + index, form);
                    input.closest('.form-group').addClass('validate-has-error');
                    input.after('<span class="text-danger">'+element+'</span>');
                });
            } else {
                alert(xhr.responseText);
            }

            setTimeout(function() {
                $('input[type="submit"], button[type="submit"]', form).prop('disabled', false);
            }, 800);
        });
    });

    $(formSelector + ' #file').on('fileSet', function(e) {
        var result = getFileImage($(this).val());

        $('.modal-gallery-image img').removeClass('not-photo');
        if (! result.isPhoto) {
            $('.modal-gallery-image img').addClass('not-photo');
        }
        $('.modal-gallery-image img').attr('src', result.file);
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
@endif