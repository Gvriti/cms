<footer id="footer">
    <div class="container">
        <div class="copyright text-center">&copy; <a href="http://digitaldesign.ge/" target="_blank">Digital Design</a></div>
        <!-- .copyright -->
    </div>
    <!-- .container -->
</footer>
<!-- #footer -->
<script src="{{asset('assets/site/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/site/js/custom.js')}}"></script>
@if (AuthCms::check())
<script src="{{ asset('assets/js/trans.js') }}"></script>
<div id="translations" data-trans-url="{{cms_route('translations.form')}}" data-token="{{csrf_token()}}"></div>
@endif