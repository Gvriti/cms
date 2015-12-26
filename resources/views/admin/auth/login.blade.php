<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="cms">
    <meta name="author" content="David Gvritishvili [gvritishvili.david@gmail.com]">
    <title>CMS - Login</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts/font-awesome-4.5.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/xenon-core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/xenon-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/xenon-components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/xenon-skins.css') }}">
    <script src="{{ asset('assets/js/jquery-1.11.3.min.js') }}"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="page-body login-page {{$settings->get('skin_login')}}">
    <div class="login-container">
        <div class="row">
            <div class="col-sm-6">
            @if ($errors->has())
                <div class="errors-container">
                    <span class="text-danger">{{$errors->first('email')}}</span>
                </div>
            @endif
                <form action="{{cms_route('login')}}" method="post" role="form" id="login" class="login-form fade-in-effect">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="login-header">
                        <a href="{{cms_route('login')}}" class="logo">
                            <img src="{{ asset('assets/images/logo@2x.png') }}" height="24" alt="Digital Design">
                            <span>{{trans('auth.login')}}</span>
                        </a>
                        <p>{{trans('auth.login_msg')}}</p>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('attributes.email')}}</label>
                        <input type="text" class="form-control input-dark" name="email" id="email" autocomplete="off" tabindex="1" autofocus>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('attributes.password')}}</label>
                        <input type="password" class="form-control input-dark" name="password" id="password" autocomplete="off" tabindex="2">
                    </div>
                    <div class="form-group">
                        <ul class="icheck-list">
                            <li>
                                <input tabindex="3" type="checkbox" name="remember" class="icheck" id="remember">
                                <label>{{trans('auth.remember_me')}}</label>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary btn-block text-left">
                            <i class="fa fa-lock"></i>
                            {{trans('auth.login')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">

<script src="{{ asset('assets/js/jquery-validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    // Reveal Login form
    setTimeout(function(){ $(".fade-in-effect").addClass('in'); }, 1);

    // Style Checkbox
    $('input.icheck').iCheck({
        checkboxClass: 'icheckbox_square-blue'
    });

    // Login Form Label Focusing
    $(".login-form .form-group:has(label)").each(function(i, e) {
        var $this = $(e),
            $label = $this.find('label'),
            $input = $this.find('input');

        $input.on('focus', function() {
            $this.addClass('is-focused');
        });

        $input.on('keydown', function() {
            $this.addClass('is-focused');
        });

        $input.on('blur', function() {
            $this.removeClass('is-focused');

            if($input.val().trim().length > 0) {
                $this.addClass('is-focused');
            }
        });

        $label.on('click', function() {
            $input.focus();
        });

        if($input.val().trim().length > 0) {
            $this.addClass('is-focused');
        }
    });
    
    // Validation
    $("form#login").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        },
        messages: {
            email: {
                required: '{{trans('auth.required.email')}}',
                email: '{{trans('auth.invalid.email')}}'
            },
            password: {
                required: '{{trans('auth.required.password')}}'
            }
        },
    });
})
</script>
</body>
</html>
