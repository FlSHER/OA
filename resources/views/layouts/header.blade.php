<div class="header-section">

    <!--toggle button start-->
    <a class="toggle-btn visible-xs" onclick="$('body').toggleClass('left-side-show');"><i class="fa fa-bars"></i></a>
    <!--toggle button end-->

    <!--nav start-->
    <nav class="navbar-default hidden-xs" style="background:none;">
        <ul class="nav navbar-nav page-tabs">

        </ul>
    </nav>
    <!--nav end-->

    <!--notification menu start -->
    <div class="menu-right">
        <ul class="notification-menu">
            <li>
                <a href="{{asset(route('entrance'))}}" class="btn btn-default dropdown-toggle info-number" title="应用首页">
                    <i class="fa fa-tablet"></i>
                    <!--<span class="badge">4</span>-->
                </a>
            </li>
            <li>
                <a href="#" class="btn btn-default dropdown-toggle " data-toggle="dropdown">
                    <!--<img src="{{asset('images/photos/user-avatar.png')}}" alt="" />-->
                    @if(empty(session('admin')['realname'])){{session('admin')['username']}}@else{{session('admin')['realname']}}@endif
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                    <?php if ($authority->checkAuthority(85)) { ?>
                        <li><a href="{{asset(route('system.flush_cache'))}}"><i class="fa fa-warning"></i> 清除缓存</a></li>
                    <?php } ?>
                    <?php if ($authority->checkAuthority(86)) { ?>
                        <li><a href="{{asset(route('personal.refresh_authority'))}}"><i class="fa fa-refresh"></i> 更新权限</a></li>
                    <?php } ?>
                    <li><a href="{{asset(route('reset'))}}"><i class="fa fa-rotate-left"></i> 重置密码</a></li>
                    <li><a href="{{asset(route('logout'))}}"><i class="fa fa-sign-out"></i> 退出登录</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!--notification menu end -->

</div>
