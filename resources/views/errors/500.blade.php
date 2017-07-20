<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">
        <link rel="shortcut icon" href="#" type="image/png">

        <title>阿喜杰尼威尼服饰有限公司</title>

        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        <link href="{{asset('css/style-responsive.css')}}" rel="stylesheet">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="error-page">
        <section>
            <div class="container ">
                <section class="error-wrapper text-center">
                    <h1><img alt="" src="{{asset('images/500-error.png')}}"></h1>
                    <h2>╮(╯▽╰)╭ 出错了</h2>
                    <h3>{{$exception->getMessage()}}</h3>
                    <p class="nrml-txt">尝试<a href="{{url()->current()}}">刷新网页</a> 或 <a href="#">联系管理员</a></p>
                    <a class="back-btn" href="{{route('entrance')}}"> 回到首页</a>
                </section>
            </div>
        </section>
    </body>
</html>
