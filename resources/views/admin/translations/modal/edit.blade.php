<div class="modal fade" id="translations-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul class="nav nav-tabs">
                @foreach ($items as $current)
                    <li{!!language() != $current->language ? '' : ' class="active"'!!}>
                        <a href="#item-{{$current->language}}" data-toggle="tab">{{languages($current->language)}}</a>
                    </li>
                @endforeach
                </ul>
            </div>
            <div class="tab-content">
            @foreach ($items as $current)
                <div class="tab-pane{{language() != $current->language ? '' : ' active'}}" id="item-{{$current->language}}">
                    {!! Form::model($current, [
                        'method' => 'post',
                        'url'    => route('translations.form', [], $current->language),
                        'class'  => 'form-horizontal ajax-form',
                        'data-lang'  => $current->language
                    ]) !!}
                        <input type="hidden" name="id" value="{{$current->id}}">
                        @include('admin.translations.modal.form')
                    {!! Form::close() !!}
                </div>
            @endforeach
            </div>
        </div>
    </div>
@include('admin.translations.modal.scripts')
</div>