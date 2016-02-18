<script type="text/javascript">
$(function () {
    var modalSelector = $('#localization-modal');

    $('form', modalSelector).on('submit', function (e) {
        e.preventDefault();
        var form  = $(this);
        var input = form.serialize();
        $('.form-group', form).find('.text-danger').remove();

        var lang = form.data('lang');

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: input,
            success: function(data) {
                $('.modal-footer', form).prepend(
                    $('<span class="text-success">saved</span>').delay(1000).fadeOut(300, function () {
                        $(this).remove();
                    })
                ).fadeIn(300);

                if (! lang || lang == '{{$currentLang = language()}}') {
                    $('[data-trans="'+data.name+'"]').text(data.value);
                }
                if (! lang) {
                @if (count(languages()) > 1)
                    modalSelector.removeClass('fade');
                    var ev = jQuery.Event('click');
                    ev.f2 = true;
                    $('[data-trans="'+data.name+'"]').trigger(ev);
                @endif
                    modalSelector.modal('hide');
                }
            },
            error: function(xhr) {
                if (xhr.status == 422) {
                    var data = xhr.responseJSON;

                    $.each(data, function(index, element) {
                        var field = $('#' + index + lang, form);
                        var errorMsg = '<div class="text-danger">'+element+'</div>';
                        field.after(errorMsg);
                    });
                } else {
                    alert(xhr.responseText);
                }
            }
        });
    });

    $('form [name="value"]', modalSelector).on('keyup', function (e) {
        var lang = $(this).closest('form').data('lang');

        if (! lang || lang == '{{$currentLang}}') {
            $('[data-trans="{{$current->name}}"]').text($(this).val());
        }
    });

    modalSelector.modal('show');
    modalSelector.on('hidden.bs.modal', function () {
        $(this).remove();
    });
});
</script>