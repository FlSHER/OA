"use strict";
$(function () {
    // Toggle Left Menu
    jQuery('.menu-list > a').click(function () {
        var parent = jQuery(this).parent();
        var sub = parent.find('> ul');
        if (sub.is(':visible')) {
            sub.slideUp(200, function () {
                parent.removeClass('nav-active');
                jQuery('.main-content').css({height: ''});
            });
        } else {
            visibleSubMenuClose();
            parent.addClass('nav-active');
            sub.slideDown(200, function () {
            });
        }
        return false;
    });

    function visibleSubMenuClose() {
        jQuery('.menu-list').each(function () {
            var t = jQuery(this);
            if (t.hasClass('nav-active')) {
                t.find('> ul').slideUp(200, function () {
                    t.removeClass('nav-active');
                });
            }
        });
    }

    $(document).on('click', '.left-side a,.page-tabs a', function () {
        var url = $(this).attr('href');
        if (url !== 'javascript:' && !$(this).parent().hasClass('active')) {
            pageLoader.jump(url);
        }
        return false;
    });

    window.addEventListener('popstate', function (event) {
        pageLoader.load();
    });

    pageLoader.load();

    /* 周期性请求，保持session */
    var connectionInterval = setInterval(keepConnectionAlive, 3600000);
});

var pageLoader = new PageLoader();

function keepConnectionAlive() {
    var url = '/blank';
    $.ajax({
        type: 'GET',
        url: url,
        success: function (msg) {
            console.log(msg);
        }
    });
}

function PageLoader() {
    this.contentBox = $('#content');
    this.menu = $('.left-side');
    this.nav = $('.page-tabs');
    this.menuTabs = this.menu.find('li');
    this.uri = null;
    this.params = null;
    this.iframePath = null;
    this.pageTitle = null;
    this.maxTag = 5;

    var pageLoader = this;

    this.jump = function (uri) {
        history.pushState({}, '', uri);
        pageLoader.load();
    };

    this.load = function () {
        this.uriInit();
        $('body').removeClass('left-side-show');
        /* 内容页 */
        this.contentBox.find('iframe').hide();
        this.showContent();
        /* 左边菜单 */
        this.menuTabs.removeClass('active').removeClass('nav-active');
        this.selectMenuTab();
        /* 顶部导航 */
        this.nav.find('li.active').removeClass('active');
        this.selectNavTab();
        if (this.nav.find('li.active').length === 0) {
            this.addNavTab();
        }
    };

    this.reload = function () {
        oaWaiting.show();
        var curIframe = pageLoader.contentBox.find('iframe:visible');
        curIframe.attr('src', curIframe.attr('src'));
        return false;
    };

    this.close = function () {
        var navTab = pageLoader.nav.find('li').has(this);
        var index = navTab.index();
        var iframe = pageLoader.contentBox.find('iframe').eq(index);
        navTab.remove();
        iframe.remove();
        return false;
    };

    /**
     * 路径初始化
     */
    this.uriInit = function () {
        this.uri = window.location.protocol + '//' + window.location.host + window.location.pathname.replace(/\/*$/, '');
        this.params = window.location.search.length > 0 ? window.location.search + '&iframe=1' : '?iframe=1';
        this.iframePath = this.uri + this.params;
    };

    /**
     * 显示页面内容
     */
    this.showContent = function () {
        var self = this;
        var iframes = this.contentBox.find('iframe');
        iframes.each(function () {
            if ($(this).attr('src') === self.iframePath) {
                $(this).show();
            }
        });
        if (iframes.filter(':visible').length === 0) {
            this.addContent();
        }
    };

    /**
     * 添加页面内容
     */
    this.addContent = function () {
        oaWaiting.show();
        if (this.contentBox.find('iframe').length >= this.maxTag) {
            this.contentBox.find('iframe:first').remove();
        }
        $('<iframe>').attr({src: this.iframePath, frameborder: 0, width: '100%', height: '100%'})
                .appendTo(this.contentBox)
                .on('load', function () {
                    oaWaiting.hide();
                });
    };

    /**
     * 选中左侧菜单
     */
    this.selectMenuTab = function () {
        var self = this;
        this.menuTabs.find('a').each(function () {
            if ($(this).attr('href') === self.uri + window.location.search || $(this).attr('href') === self.uri + '/' + window.location.search) {
                self.pageTitle = $(this).text();
                $(this).parents('li').addClass('active').not(':eq(0)').last().addClass('nav-active').children('ul').show();
            }
        });
    };

    /**
     * 选中导航标签
     */
    this.selectNavTab = function () {
        var self = this;
        this.nav.find('li a').each(function () {
            if ($(this).attr('href') === self.uri + window.location.search || $(this).attr('href') === self.uri + '/' + window.location.search) {
                $(this).parent().addClass('active');
            }
        });
    };

    /**
     * 添加新导航标签
     */
    this.addNavTab = function () {
        if (this.nav.find('li').length >= this.maxTag) {
            this.nav.find('li:first').remove();
        }
        var tab = $('<li>').addClass('active').appendTo(this.nav);
        var link = $('<a>').text(this.pageTitle).attr({'href': this.uri + window.location.search}).appendTo(tab);
        var refreshBtn = $('<i>').addClass('fa fa-fw fa-refresh refresh-btn').appendTo(link).on('click', this.reload);
        var closeBtn = $('<i>').addClass('fa fa-fw fa-times close-btn').appendTo(link).on('click', this.close);
    };
}

