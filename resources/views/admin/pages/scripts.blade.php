@push('styles.head')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
@endpush
@push('scripts.bottom')
<script type="text/javascript">
$(function() {
    $('.select').select2({
        placeholder: 'Select item',
        allowClear: true
    }).on('select2-open', function() {
        // Add custom scrollbar
        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
    });

    $('.panel form [name="type"].select').on('change', function(){
        var typeId = $('.panel form .type-id').addClass('hidden');
        var template = $('.panel form .template');
        var templateSelect = $('select', template).html('<option value=""></option>');

        // Get templates list
        $.post('{{cms_route('pages.templates')}}', {'type':this.value, '_token':'{{csrf_token()}}'}, function (data) {
            if (data.length != 0) {
                template.removeClass('hidden');

                $.each(data, function (key, value) {
                    templateSelect.append('<option value="'+key+'">'+value+'</option>');
                });

                templateSelect.select2('val', '');
            } else {
                template.addClass('hidden');
            }
        }, 'json').fail(function (xhr) {
            alert(xhr.responseText);
        });

        if (["{!!implode('","', (array) cms_pages('attached'))!!}"].indexOf(this.value) != -1) {
            $('label', typeId).text($(this).val());
            typeId.removeClass('hidden');
            template.addClass('hidden');
        }
    });
});
</script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@endpush
