@inject('authority','Authority')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" >  
        <meta name="description" content="">
        <meta name="author" content="Fisher">
        <meta name="_token" content="{{csrf_token()}}">
        <link rel="shortcut icon" href="{{source('images/logo_icon.png')}}" type="image/png">
        <title>@section('title')喜歌实业@show</title>
        <link href="{{source('css/style.css')}}" rel="stylesheet">
        <link href="{{source('css/style-responsive.css')}}" rel="stylesheet">
    </head>

    <body class="sticky-header">
        <section>
            @section('left_side')
            <!-- left side start-->
            @include('layouts.left_side')
            <!-- left side end-->
            @show

            <!-- main content start-->
            <div class="main-content">
                @section('header')
                <!-- header section start-->
                @include('layouts.header')
                <!-- header section end-->
                @show
                <div class="iframe-box" id="content"></div>
            </div>
            <!-- main content end-->
        </section>
        <!-- waiting start -->
        <div id="waiting">
            <i class="fa fa-spinner fa-pulse fa-5x fa-inverse" style="position:absolute;top:48%;margin-top:-35px;"></i>
        </div>
        <!-- waiting end -->

        <!-- Placed js at the end of the document so the pages load faster -->
        <script src="{{source('js/jquery-3.2.1.min.js')}}"></script>
        <script src="{{source('js/bootstrap.js')}}"></script>
        <!--common scripts for all pages-->
        <script src="{{source('js/scripts.js')}}"></script>
        <script src="{{source('js/layout/layout.js')}}"></script>
    </body>
</html>
