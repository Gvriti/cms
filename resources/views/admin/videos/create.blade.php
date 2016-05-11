@if (! empty($current))
<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-gallery-image embed-responsive embed-responsive-16by9">
                <iframe width="600" height="315" src="" frameborder="0" allowfullscreen class="embed-responsive-item"></iframe>
            </div>
            {!! Form::model($current, [
                'url'   => cms_route('videos.store', [$current->gallery_id]),
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
                                <label class="control-label">Video Link:</label>
                                {!! Form::text('file', null, [
                                    'id' => 'file',
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Visible:</label>
                                {!! Form::checkbox('visible', null, null, [
                                    'id' => 'visible',
                                    'class' => 'iswitch iswitch-secondary'
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
@push('scripts.bottom')
<script type="text/javascript">
    var sort = '{{request('sort', 'desc')}}';
    var currentPage = {{request('page', 1)}};
    var creationPage = sort == 'desc' ? 1 : {{request('lastPage', 1)}};
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

            if (currentPage != creationPage) {
                window.location.href = '{{cms_route('videos.index', [$current->gallery_id])}}?page=' + creationPage;
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
        });
    });
</script>
@endpush
@endif