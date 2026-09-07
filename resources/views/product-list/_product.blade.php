<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Quicktax</title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	@yield('header_metas')

	@yield('header_style_preload')

    <link href="{{ asset('/assets/css/main.css')}}" rel="stylesheet" type="text/css" />
    
	@yield('header_style_postload')

</head>
<body class="skin-blue @yield('body_classes')">

    @yield('content')

    <!-- JavaScripts -->
    {{-- 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="{{ elixir('js/app.js') }}"></script> 
	--}}
	
	<script src="{{asset('/assets/js/vendor.js')}}"></script>
	@yield('footer_script_preload')
	<script src="{{asset('/assets/js/app.js')}}"></script>
	@yield('footer_script')
</body>
</html>
