@extends('admin.app')
@section('content')
<div class="page-title">
    <div class="title-env">
        <h1 class="title">
            <i class="{{icon_type('photos')}}"></i>
            Slider
        </h1>
    </div>
    <div class="breadcrumb-env">
        <ol class="breadcrumb bc-1">
            <li>
                <a href="{{ cms_url() }}"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li class="active">
                <a href="{{ cms_route('galleries.index') }}">
                    <i class="{{icon_type('galleries')}}"></i>
                    Slider
                </a>
            </li>
        </ol>
    </div>
</div>
<section class="gallery-env">
    <div class="row">
        <div class="col-sm-12">
            <div class="album-header">
                <h2>Slider</h2>
                <ul class="album-options list-unstyled list-inline">
                    <li>
                        <input type="checkbox" class="cbr" id="select-all" />
                        <label>Select all</label>
                    </li>
                    <li>
                        <a href="#" data-modal="add">
                            <i class="{{icon_type('photos')}}"></i>
                            Add Image
                        </a>
                    </li>
                    <li>
                        <a href="#" data-action="sort">
                            <i class="fa fa-arrows"></i>
                            Re-order
                        </a>
                    </li>
                    <li>
                        <a href="#" data-modal="multiselect">
                            <i class="fa fa-edit"></i>
                            Edit
                        </a>
                    </li>
                    <li>
                        <a href="#" data-delete="multiselect">
                            <i class="fa fa-trash"></i>
                            Trash
                        </a>
                    </li>
                </ul>
            </div>
            <div class="album-sorting-info">
                <div class="album-sorting-info-inner clearfix">
                    <a href="#" id="save-tree" class="btn btn-secondary btn-xs btn-single btn-icon btn-icon-standalone pull-right" data-action="sort">
                        <i class="fa fa-save"></i>
                        <span>Save Current Order</span>
                    </a>
                    <i class="fa fa-arrows-alt"></i>
                    Drag images to sort them
                </div>
            </div>
            <ul id="nestable-list" class="album-images list-unstyled row" data-insert="prepend" data-uk-nestable="{maxDepth:1}">
            @foreach($items as $item)
                <li id="item{{$item->id}}" data-id="{{$item->id}}" data-pos="{{$item->position}}" data-url="{{cms_route('slider.edit', [$item->id])}}" class="item col-lg-3 col-md-4 col-sm-4 col-xs-6">
                    <div class="album-image">
                        <a href="#" class="thumb" data-modal="edit">
                            <img src="{{$item->file ?: $item->file_default}}" class="img-responsive" alt="{{$item->title}}" />
                        </a>
                        <a href="#" class="name">
                            <span class="title">{{$item->title}}</span>
                            <em>{{$item->created_at->format('d F Y')}}</em>
                        </a>
                        <div class="image-options">
                            <a href="#" data-url="{{cms_route('slider.visibility', [$item->id])}}" class="visibility" title="{{trans('general.visibility')}}">
                                <i class="fa fa-eye{{$item->visible ? '' : '-slash'}}"></i>
                            </a>
                            <a href="#" data-modal="edit" title="{{trans('general.edit')}}"><i class="fa fa-pencil"></i></a>
                            <a href="#" data-delete="this" data-id="{{$item->id}}" title="{{trans('general.delete')}}"><i class="fa fa-trash"></i></a>
                        </div>
                        <div class="image-checkbox select-item">
                            <input type="checkbox" data-id="{{$item->id}}" class="cbr" />
                        </div>
                        <div class="btn-action"></div>
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
    </div>
</section>
<script type="text/javascript">
$(document).ready(function($) {
    var galleryEnv = $('.gallery-env');

    // Select all items
    $("#select-all").on('change', function(e) {
        var is_checked = $(this).is(':checked');
        $(".album-image input[type='checkbox']").prop('checked', is_checked).trigger('change');
    });

    var multiselect = [];

    // Call Modal
    galleryEnv.on('click', 'a[data-modal]', function(e) {
        e.preventDefault();
        var action = $(this).data('modal');
        if (action == 'edit') {
            var item = $(this).closest('.item');
            var url = item.data('url');

            $.get(url, function(data) {
                galleryEnv.append(data.view);

                $("#form-modal").modal('show');
            }, 'json').fail(function(xhr) {
                alert(xhr.responseText);
            });

            if (multiselect.length) {
                multiselect.splice(0, 1);
            }
        } else if (action == 'add') {
            $.get("{!!cms_route('slider.create')!!}", function(data) {
                galleryEnv.append(data.view);

                $("#form-modal").modal('show');
            }, 'json').fail(function(xhr) {
                alert(xhr.responseText);
            });
        } else if (action == 'multiselect') {
            $('.album-image input:checked').each(function() {
                multiselect.push($(this).data('id'));
            });

            if (multiselect.length) {
                $('#item'+multiselect[0]+' .thumb[data-modal="edit"]').trigger('click');
            }
        }
    });

    // on hide modal event
    galleryEnv.on('hidden.bs.modal', '#form-modal', function () {
        if (multiselect.length) {
            $('#item'+multiselect[0]+' .thumb[data-modal="edit"]').trigger('click');
        }

        $(this).remove();
    });

    // Delete item(s)
    galleryEnv.on('click', 'a[data-delete]', function(e) {
        e.preventDefault();
        var action = $(this).data('delete');
        if (action == 'multiselect') {
            var perform = confirm("{{trans('general.delete_selected_confirm')}}");
            if (perform != true) return;

            var ids = [];
            $('.select-item input:checked', galleryEnv).each(function(i, e) {
                ids.push($(e).data('id'));
            });
        } else {
            var perform = confirm("{{trans('general.delete_confirm')}}");
            if (perform != true) return;
            var ids = [$(this).data('id')];
        }

        if (ids.length) {
            var input = {'ids':ids, '_method':'delete', '_token':csrf_token()};

            $.post("{{cms_route('slider.index')}}/" + ids, input, function(data) {
                // alert toastr message
                toastr[data.result](data.message);

                if (data.result == 'success') {
                    $.each(ids, function(i, e) {
                        $('#item'+e, galleryEnv).remove();
                    });
                }
            }, 'json').fail(function(xhr) {
                alert(xhr.responseText);
            });
        }
    });

    // visibility of the item
    galleryEnv.on('click', '.visibility', function(e) {
        e.preventDefault();
        var item = $(this);
        var url = item.data('url');

        var input = {'_token':csrf_token()};
        $.post(url, input, function(data) {
            if (data.visible) {
                value = 1;
                var icon = 'fa-eye';
            } else {
                value = 0;
                var icon = 'fa-eye-slash';
            }
            item.find('i').attr('class', icon);
        }, 'json').fail(function(xhr) {
            alert(xhr.responseText);
        });
    });

    // Sortable
    $('.gallery-env a[data-action="sort"]').on('click', function(e) {
        e.preventDefault();

        var is_sortable = $(".album-images").sortable('instance');

        if( ! is_sortable) {
            $(".gallery-env .album-images").sortable({
                items: '> li',
                containment: 'parent'
            });

            $(".album-sorting-info").stop().slideDown(300);
            $('#save-tree').show().prop('disabled', false);
        } else {
            $(".album-images").sortable('destroy');
            $(".album-sorting-info").stop().slideUp(300);
        }
    });

    positionable('{{ cms_route('slider.updatePosition') }}', 'desc');
});
</script>

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>
<script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>
@endsection
