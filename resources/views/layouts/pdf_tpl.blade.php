<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bigbyte Aqua Ventures</title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    @yield('header_style_preload')
    <link href="{{ asset('/assets/css/print.css')}}" rel="stylesheet" type="text/css" />
    @yield('header_style_postload')

</head>
<body class="@yield('body_classes')">

    <div class="wrapper">
        @yield('content')
    </div>

</body>
</html>
