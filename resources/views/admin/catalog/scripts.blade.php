<script type="text/javascript">
$(function() {
    $("form .options").multiSelect({
        afterInit: function() {
            this.$selectableContainer.add(this.$selectionContainer).find('.ms-list').perfectScrollbar();
        },

        afterSelect: function() {
            this.$selectableContainer.add(this.$selectionContainer).find('.ms-list').perfectScrollbar('update');
        },

        selectableHeader: "<label>პარამეტრები</label>",

        selectionHeader: "<label>შერჩეული პარამეტრები</label>"
    });

    $('form.ajax-form').on('ajaxFormSuccess', function() {
        $("form .options").multiSelect('refresh');
    });
});
</script>