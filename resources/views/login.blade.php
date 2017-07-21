<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="Fisher">
        <meta name="csrf_token" content="{{csrf_token()}}">
        <link rel="shortcut icon" href="{{source('images/logo_icon.png')}}" type="image/png">
        <title>喜歌实业1234</title>
        <link href="{{source('css/style.css')}}" rel="stylesheet">
        <link href="{{source('css/style-responsive.css')}}" rel="stylesheet">
        <style>
            .login-body {
                background: #65cea7 url("../images/login-bg.jpg") no-repeat fixed;
                background-size: cover;
                width: 100%;
                height: 100%;
            }

            .form-signin {
                max-width: 330px;
                margin: 100px auto;
                background: #fff;
                border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .form-signin .form-signin-heading {
                margin: 0;
                padding: 25px 15px;
                text-align: center;
                color: #fff;
                position: relative;
            }

            .sign-title {
                font-size: 24px;
                color: #fff;
                position: absolute;
                top: -60px;
                left: 0;
                text-align: center;
                width: 100%;
                text-transform: uppercase;
            }

            .form-signin .checkbox {
                margin-bottom: 14px;
                font-size: 13px;
            }

            .form-signin .checkbox {
                font-weight: normal;
                color: #fff;
                font-weight: normal;
                font-family: 'Open Sans', sans-serif;
                position: absolute;
                bottom: -50px;
                width: 100%;
                left: 0;
            }

            .form-signin .checkbox a, .form-signin .checkbox a:hover {
                color: #fff;
            }

            .form-signin .form-control {
                position: relative;
                font-size: 16px;
                height: auto;
                padding: 10px;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }

            .form-signin .form-control:focus {
                z-index: 2;
            }

            .form-signin input[type="text"], .form-signin input[type="password"] {
                margin-bottom: 15px;
                border-radius: 5px;
                -webkit-border-radius: 5px;
                border: 1px solid #eaeaec;
                background: #eaeaec;
                box-shadow: none;
                font-size: 12px;
            }

            .form-signin .btn-login {
                background: #6bc5a4;
                color: #fff;
                text-transform: uppercase;
                font-weight: normal;
                font-family: 'Open Sans', sans-serif;
                margin: 20px 0 5px;
                padding: 5px;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                transition: all 0.3s;
                font-size: 30px;
            }

            .form-signin .btn-login:hover {
                background: #688ac2;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                transition: all 0.3s;
            }

            .form-signin p {
                text-align: left;
                color: #b6b6b6;
                font-size: 16px;
                font-weight: normal;
            }

            .form-signin a, .form-signin a:hover {
                color: #6bc5a4;
            }

            .form-signin a:hover {
                text-decoration: underline;
            }

            .login-wrap {
                padding: 20px;
                position: relative;
            }

            .registration {
                color: #c7c7c7;
                text-align: center;
                margin-top: 15px;
            }
        </style>

    </head>
    @if($errors->get('login_fail'))
    <div class="alert-block alert-danger fade in" style='padding:15px;position:absolute;width:100%;'>
        <button type="button" class="close close-sm" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        <strong>登录失败：</strong> {!! $errors->get('login_fail')['0'] !!}
    </div>
    @endif
    <body class="login-body">
        <div class="container">
            <form class="form-signin" action="" method="post">
                <div class="form-signin-heading text-center">
                    <h1 class="sign-title"></h1>
                    <img src="{{source('images/login-logo.png')}}" alt=""/>
                </div>
                <div class="login-wrap">
                    <input type="text" name="username" class="form-control" placeholder="用户名/手机号" autofocus>
                    <input type="password" name="password" class="form-control" placeholder="密码（默认：123456）">
                    {!! csrf_field() !!}
                    <input type="hidden" name="url" value="{{session('url')}}">
                    <button class="btn btn-md btn-login btn-block" type="submit">
                        <i class="fa fa-check"></i>
                    </button>
                    <div class="registration">
                    </div>
                </div>
            </form>
        </div>

        <!-- waiting start -->
        <div id="waiting" style="left:0;top:0;">
            <i class="fa fa-spinner fa-pulse fa-5x fa-inverse" style="position:absolute;top:48%;margin-top:-35px;"></i>
        </div>
        <!-- waiting end -->

        <!-- 应用登出 start -->
        @foreach($app as $v)
        @if($v->logout_url)
        <!--{{$v->name}}-->
        <iframe src="{{$v->logout_url}}" class="hidden"></iframe>
        @endif
        @endforeach
        <!-- 应用登出 end -->

        <script type="text/javascript">
            var CorpId = '<?php echo config('dingding.CorpId'); ?>';
        </script>

        <!-- Placed js at the end of the document so the pages load faster -->
        <script src="{{source('js/jquery-3.1.1.min.js')}}"></script>
        <script src="{{source('js/bootstrap.min.js')}}"></script>
        <script src="{{source('js/scripts.js')}}"></script>
        <script src="http://g.alicdn.com/dingding/open-develop/1.5.1/dingtalk.js"></script>
        <script src="{{source('js/dingding_login.js')}}"></script>

    </body>
</html>
