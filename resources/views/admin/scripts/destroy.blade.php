$('.form-delete button[type="submit"], .form-delete input[type="submit"]').on('click', function(e) {
    e.preventDefault();
    var perform = confirm("{{trans('general.delete_confirm')}}");
    if (perform != true) return;
    var item = $(this);
    var itemId = $(this).data('id');
    item.prop('disabled', true);

    var form       = $(item).closest('form');
    var formAction = form.attr('action');

    var input = $(form).serializeArray();
    $.ajax({
        type: 'POST',
        url: formAction,
        dataType: 'json',
        data: input,
        success: function(data, status, xhr) {
            if (data) {
                $('body').append(data.view);
                if (data.result) {
                    $('#item' + itemId).fadeOut(600, function() {
                    @if(isset($subTree))
                        $(this).closest('.uk-parent').removeClass('uk-parent');
                        disableParentDeletion();
                    @endif
                        item.remove();
                    });
                }
            }

            item.prop('disabled', false);
        },
        error: function(xhr) {
            item.prop('disabled', false);

            alert(xhr.responseText);
        }
    });
});
