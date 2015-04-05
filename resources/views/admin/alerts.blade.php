@if (session()->has('alert'))
<div id="alert">
    <script>
    $(function(){
        $("#alert").remove();
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-{{$settings->get('alert_position')}}",
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "4000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        toastr["{{$result = session()->pull('alert.result')}}"]("{{session()->pull('alert.message')}}", "{{trans('general.'.$result)}}!");
    });
    </script>
</div>
{{session()->forget('alert')}}
@endif
