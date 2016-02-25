<script type="text/javascript">
$(function() {
    $('.select').select2({
        placeholder: 'Select item',
        allowClear: true
    }).on('select2-open', function() {
        // Adding Custom Scrollbar
        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
    });

    $('.panel form [name="type"].select').on('click', function(){
        var collection = $('.panel form .collection').addClass('hidden');
        var template = $('.panel form .template');
        var templateSelect = $('select', template).html('<option value=""></option>');

        if (this.value != 'collections') {
            // Get templates list
            $.post('{{cms_route('pages.templates')}}', {'type':this.value, '_token':'{{csrf_token()}}'}, function (data) {

                if (data.length != 0) {
                    console.log(data);
                    template.removeClass('hidden');

                    $.each(data, function (key, value) {
                        templateSelect.append('<option value="'+key+'">'+value+'</option>');
                    });

                    templateSelect.select2('val', '');
                } else {
                    template.addClass('hidden');
                }
            }, 'json').fail(function (xhr, status, error) {
                alert(xhr.responseText);
            });
        } else {
            collection.removeClass('hidden');
            template.addClass('hidden');
        }
    });
});
</script>

<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>