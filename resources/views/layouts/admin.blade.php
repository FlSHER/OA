@inject('authority','Authority')
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" >  
        <meta name="description" content="">
        <meta name="_token" content="{{csrf_token()}}">
        @yield('css')
        <link href="{{source('css/style.css')}}" rel="stylesheet">
        <link href="{{source('css/style-responsive.css')}}" rel="stylesheet">
        <!-- flatpickr -->
        <link href="{{source('plug_in/flatpickr/flatpickr.min.css')}}" rel="stylesheet">
        <link href="{{source('plug_in/flatpickr/themes/airbnb.css')}}" rel="stylesheet">
        <!-- jQuery -->
        <script src="{{source('js/jquery-3.2.1.min.js')}}"></script>
    </head>

    <body class="sticky-header">
        <!--body wrapper start-->
        <div class="wrapper">
            @yield('content')
        </div>
        <!--body wrapper end-->

        <!-- Placed js at the end of the document so the pages load faster -->
        <script src="{{source('js/bootstrap.min.js')}}"></script>
        <script src="{{source('js/scripts.js')}}"></script>
        <!-- flatpickr -->
        <script src="{{source('plug_in/flatpickr/flatpickr.min.js')}}"></script>
        <script src="{{source('plug_in/flatpickr/localization/zh.js')}}"></script>
        @yield('js')
    </body>
</html>