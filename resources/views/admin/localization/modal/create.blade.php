<div class="modal fade" id="localization-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Localization</h4>
            </div>
            {!! Form::model($current, [
                'method' => 'post',
                'url'    => cms_route('localization.form'),
                'class'  => 'form-horizontal ajax-form'
            ]) !!}
                @include('admin.localization.modal.form', [
                    'lang' => null,
                ])
            {!! Form::close() !!}
        </div>
    </div>
@include('admin.localization.modal.scripts')
</div>