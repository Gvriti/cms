@if (! empty($current))
<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-gallery-image">
                <img src="{{$current->file ?: $current->file_default}}" class="img-responsive" />
            </div>
            {!! Form::model($current, [
                'url'   => cms_route('slider.store'),
                'class' => 'form-create form-horizontal'
            ]) !!}
                <div class="modal-body">
                    <div class="row">
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
                                <label class="control-label">Short description:</label>
                                {!! Form::textarea('description', null, [
                                    'id' => 'description',
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
                    </div>
                </div>
            {!!Form::close()!!}
        </div>
    </div>
</div>
<script type="text/javascript">
    var formSelector = '#form-modal .form-create';

    $(formSelector).on('submit', function(e) {
        e.preventDefault();

        form = $(this);
        $('.form-group', form).find('.text-danger').remove();
        var url = form.attr('action');
        var input = form.serialize();
        $.post(url, input, function(data) {
            // alert toastr message
            toastr[data.result](data.message);

            var imageContainer = '.gallery-env .album-images';
            var insert = $(imageContainer).data('insert');
            insert = Function("$('"+imageContainer+"')."+insert+"('"+data.view+"');");
            insert();

            cbr_replace();

            $('#form-modal [data-dismiss]').trigger('click');
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
        });
    });

    $(formSelector + ' #file').on('fileSet', function(e) {
        $('.modal-gallery-image img').attr('src', $(this).val());
    });
</script>
@endif