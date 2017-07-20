@inject('currentUser','CurrentUser')
@inject('authority','Authority')
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" >  
        <meta name="description" content="">
        <link rel="shortcut icon" href="{{source('images/logo_icon.png')}}" type="image/png">
        <title>阿喜杰尼威尼服饰有限公司</title>
        <link href="{{source('css/style.css')}}" rel="stylesheet">
        <link href="{{source('css/style-responsive.css')}}" rel="stylesheet">
    </head>
    <style>
        body{
            background-color:#424f63;
        }
        .app-button{padding:0;margin:0 auto;border:0;width:100%;overflow:hidden;}
        @media screen and (max-width: 900px) {
            #backstage{display:none;}
        }
    </style>
    <body>
        <div class="header-section">
            <div class="menu-right">
                <ul class="notification-menu">
                    <li>
                        <a href="{{asset(route('home'))}}" class="btn btn-default dropdown-toggle info-number" id="backstage" title="OA后台">
                            <i class="fa fa-briefcase"></i>
                            <!--<span class="badge">4</span>-->
                        </a>
                    </li>
                    <li>
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            {{$currentUser->getName()}}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                            @if ($authority->checkAuthority(86))
                            <li><a href="{{asset(route('personal.refresh_authority'))}}"><i class="fa fa-refresh"></i> 更新权限</a></li>
                            @endif
                            <li><a href="{{asset(route('reset'))}}"><i class="fa fa-cog"></i>重置密码</a></li>
                            <li><a href="{{asset(route('logout'))}}"><i class="fa fa-sign-out"></i>退出登录</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 应用图标 -->
        <section class="row" style="margin:0;">
            @foreach($app as $v)
            @if ($authority->checkAuthority($v->authority_id))
            <div class="col-lg-2 col-md-2 col-xs-3 text-center" style="padding:3%;">
                <a class="btn btn-lg app-button" href="{{$v->url}}" target="_blank">
                    <img src="{{source($v->pic_path)}}" style="width:100%;">
                </a>
                <p class="" style="color:#eee;font-size:14px;">{{$v->name}}</p>
            </div>
            @endif
            @endforeach
        </section>

        <!-- Placed js at the end of the document so the pages load faster -->
        <script src="{{source('js/jquery-3.1.1.min.js')}}"></script>
        <script src="{{source('js/bootstrap.js')}}"></script>
    </body>
</html>
