<div class="left-side sticky-left-side" >

    <!--logo and iconic logo start-->
    <div class="logo hidden-xs">
        <a><img src="{{asset('images/logo.png')}}"ni style="height:40px;" alt=""></a>
    </div>

    <div class="logo-icon text-center">
        <a><img src="{{asset('images/logo_icon.png')}}" alt=""></a>
    </div>
    <!--logo and iconic logo end-->
    <div class="left-side-inner">
        @inject('menuService','Menu')
        <?php $menus = $menuService->getMenuData(); ?>
        <?php $authorityIdArr = $authority->getAuthorityIdArr(); ?>

        <!--sidebar nav start-->
        <ul class="nav nav-stacked custom-nav">
            @foreach($menus as $menu)
            @if(isset($menu['children']))
            <li class="menu-list <?php if (in_array($menu['id'], $authorityIdArr)) echo 'nav-active'; ?>">
                <a href="javascript:"><i class="fa {{$menu['menu_logo'] or 'fa-square'}}"></i> <span>{{$menu['menu_name']}}</span></a>
                <ul class="sub-menu-list">
                    @foreach($menu['children'] as $module)
                    @if(isset($module['children']))
                    <li class="<?php if (in_array($module['id'], $authorityIdArr)) echo 'active'; ?>">
                        <a href="javascript:">{{$module['menu_name']}}<i class="fa fa-angle-right pull-right"></i></a>
                        <ul class="sub-menu-list">
                            @foreach($module['children'] as $action)
                            <li class="<?php if (in_array($action['id'], $authorityIdArr)) echo 'active'; ?>">
                                <a href="{{asset($action->full_url_tmp)}}">{{$action['menu_name']}}</a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @else
                    <li class="<?php if (in_array($module['id'], $authorityIdArr)) echo 'active'; ?>">
                        <a href="{{asset($module->full_url_tmp)}}">{{$module['menu_name']}}</a>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </li>
            @else
            <li class="<?php if (in_array($menu['id'], $authorityIdArr)) echo 'active'; ?>">
                <a href="{{asset($menu->full_url_tmp)}}"><i class="fa {{$menu['menu_logo'] or 'fa-square'}}"></i> <span>{{$menu['menu_name']}}</span></a>
            </li>
            @endif
            @endforeach
        </ul>
        <!--sidebar nav end-->
    </div>
</div>