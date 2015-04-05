<script type="text/javascript">
$(function() {
    $('.select').select2({
        placeholder: 'Select collection',
        allowClear: true
    }).on('select2-open', function() {
        // Adding Custom Scrollbar
        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
    });

    $('.panel form .type.select').on('click', function(){
        if ($(this).val() != 'collection') {
            $('.collection').addClass('hidden');
        } else {
            $('.collection').removeClass('hidden');
        }
    });
});
</script>

<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>