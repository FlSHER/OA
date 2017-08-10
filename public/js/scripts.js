"use strict";
//设置csrf_token头部
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    }
});
/**
 * 等待画面
 * @object waiting
 */
var oaWaiting = {
    query: $('#waiting', window.top.document),
    isHidden: function () {
        return this.query.is(':hidden');
    },
    show: function () {
        if (this.isHidden()) {
            this.query.fadeIn(200);
        }
    },
    hide: function () {
        if (!this.isHidden()) {
            var callback = arguments[0] ? arguments[0] : null;
            if (typeof callback === 'function') {
                this.query.fadeOut(300, callback);
            } else {
                this.query.fadeOut(300);
            }
        }
    }
};
//OA表单对象集合，全局变量
$.fn.oaForm = oaForm;
$.fn.oaDate = oaDate;
$.fn.oaDateTime = oaDateTime;
$.fn.oaFormList = oaFormList;
$.fn.oaSearch = oaSearch;
$.fn.oaTable = oaTable;
/**
 * OA表单初始化
 * @returns {OAForm|oaForm.form}
 */
function oaForm() {
    var option = arguments[0] ? arguments[0] : {};
    var formGroup = new Array();
    this.each(function () {
        if (!this.oaForm) {
            var form = new OAForm(this, option);
            this.oaForm = form;
        }
        formGroup.push(this.oaForm);
    });
    $.extend(this, formGroup);
    return this;
}

/**
 * OA时间插件初始化（带时分秒）
 * @returns {Array|oaDate.dateGroup}
 */
function oaDateTime() {
    var options = arguments[0] ? arguments[0] : {};
    var optionOrigin = {
        enableTime: true,
        enableSeconds: true
    };
    options = $.extend(true, {}, optionOrigin, options);
    return this.oaDate(options);
}

/**
 * OA时间插件初始化
 * @returns {Array|oaDate.dateGroup}
 */
function oaDate() {
    var options = arguments[0] ? arguments[0] : {};
    var optionOrigin = {
        locale: "zh",
        allowInput: true,
        onReady: function (dObj, dStr, fp) {
            fp.input.setAttribute('autocomplete', 'off');
        },
        onClose: function (selectedDates, dateStr, instance) {
            instance.input.blur();
        }
    };
    options = $.extend(true, {}, optionOrigin, options);
    var dateGroup = [];
    this.each(function () {
        var attr = this.attributes;
        for (var i in attr) {
            switch (attr[i].name) {
                case 'mindate':
                    options['minDate'] = attr[i].value;
                    break;
                case 'maxdate':
                    options['maxDate'] = attr[i].value;
                    break;
                case 'dateformat':
                    options['dateFormat'] = attr[i].value;
                    break;
                case 'defaultdate':
                    options['defaultDate'] = attr[i].value;
                    break;
                case 'defaulthour':
                    options['defaultHour'] = attr[i].value;
                    break;
                case 'defaultminute':
                    options['defaultMinute'] = attr[i].value;
                    break;
                case 'mode':
                    options['mode'] = attr[i].value;
                    break;
            }
        }
        var fp = $(this).flatpickr(options);
        dateGroup.push(fp);
    });
}

/**
 * OA表单列表初始化
 * @returns {Array|oaFormList.oaFromListGroup}
 */
function oaFormList() {
    var options = arguments[0] ? arguments[0] : {};
    var fromListGroup = [];
    this.each(function () {
        if (!this.oaFormList) {
            var formList = new OAFormList(this, options);
            this.oaFormList = formList;
        }
        fromListGroup.push(this.oaFormList);
    });
    $.extend(this, fromListGroup);
    return this;
}

/**
 * OA搜索框初始化
 * @returns {Array|oaSearch.oaSearchGroup}
 */
function oaSearch() {
    var options = arguments[0] ? arguments[0] : {};
    var oaSearchGroup = [];
    this.each(function () {
        var search = new OASearch(this, options);
        oaSearchGroup.push(search);
    });
    return oaSearchGroup;
}

/**
 * OA数据表格初始化
 * @returns {oaTable}
 */
function oaTable() {
    var options = arguments[0] ? arguments[0] : {};
    var table = new OATable(this, options);
    $.extend(this, table);
    return this;
}

/**
 * OA通用表单类
 * @param {jQuery} dom 表单DOM元素
 * @param {object} options 参数
 * @returns {OAForm}
 */
function OAForm(dom, options) {
    var self = this;
    /**
     * 默认配置
     */
    var optionOrigin = {
        //数据源
        dataSource: {
            url: null,
            type: 'POST',
            params: {},
            dataType: 'JSON'
        },
        // 回调函数,通过"_call"方法调用,this指向dom元素
        callback: {
            // 实例化回调
            afterConstruct: function (obj) {},
            // 表单提交成功
            submitSuccess: function (msg, obj) {
                alert(msg);
            },
            // 表单提交失败
            submitFail: function (msg, obj) {
                alert(msg);
            },
            // 表单验证失败
            validateError: function (msg, obj) {
                self.tooltip.raiseAll(msg);
            },
            // 表单重置回调
            afterReset: function (obj) {
                obj.query.closest('.modal').modal('show');
            },
            // 数据渲染回调
            afterFillData: function (obj) {}
        },
        oaDate: {},
        oaDateTime: {},
        oaFormList: {}
    };
    this.dom = dom;
    this.query = $(dom);
    this.units = this.query.find("input,select,textarea");
    this.setting = $.extend(true, {}, optionOrigin, options);
    this.fromList = [];
    /**
     * 构造函数
     * @returns {undefined}
     */
    this._construct = function () {
        /* 关联插件初始化 start */
        self.query.find('[isDate][isDate!=false]').oaDate(self.setting.oaDate);
        self.query.find('[isDateTime][isDateTime!=false]').oaDateTime(self.setting.oaDateTime);
        self.formList = self.query.find('[isFormList][isFormList!=false]').oaFormList(self.setting.oaFormList);
        self.query.find('[oaSearch][oaSearch!=false]').each(function () {
            $(this).oaSearch($(this).attr('oaSearch'));
        });
        /* 关联插件初始化 end */
        self.query.on('submit', self.submitByAjax);
        self.query.on('reset', self.reset);
        _call('afterConstruct');
    };
    /**
     * 获取所有表单元素
     * @returns {unresolved}
     */
    this.refreshUnits = function () {
        self.units = self.query.find('input,select,textarea');
    };
    /**
     * 表单重置
     * @returns {undefined}
     */
    this.reset = function () {
        self.tooltip.start();
        self.units.each(function () {
            var tagName = this.tagName;
            var type = $(this).attr("type");
            var lock = $(this).attr("locked");
            if (!lock) {
                if (type === "checkbox") {
                    $(this).prop("checked", false);
                } else if (tagName === "SELECT") {
                    $(this).find('option:first').select();
                } else {
                    $(this).val('');
                }
                $(this).change();
            }
        });
        $.each(self.formList, function () {
            this.reset();
        });
        _call('afterReset');
        return self;
    };
    /**
     * 使用数据渲染表单
     * @returns {undefined}
     */
    this.fillData = function () {
        var url = arguments[0] ? arguments[0] : self.setting.dataSource.url;
        var data = arguments[1] ? arguments[1] : self.setting.dataSource.params;
        var type = arguments[2] ? arguments[2] : self.setting.dataSource.type;
        if (url === null || url.length === 0) {
            return false;
        }
        $.ajax({
            url: url,
            data: data,
            type: type,
            async: false,
            dataType: self.setting.dataSource.dataType,
            success: function (msg) {
                self.dom.reset();
                $.each(self.formList, function () {
                    this.fillData(msg);
                });
                self.refreshUnits();
                self.units.each(function () {
                    var name = $(this).attr("name")
                            .replace(/^(.*?)(\[.*\])*$/, '[$1]$2')
                            .replace(/\[(\w+?)\]/g, '["$1"]');
                    var getValueCode = ('msg' + name).replace(/^(.*?)\[\](.*)$/, 'self.arrayPluck($1,\'$2\')');
                    try {
                        var value = eval(getValueCode);
                    } catch (err) {
                        var value = undefined;
                    }
                    var type = $(this).attr("type");
                    var lock = $(this).attr("locked");
                    if (typeof value !== undefined && !lock) {
                        if (type === "checkbox") {
                            var check = $.isArray(value) ? self.inArray($(this).val(), value) : $(this).val() == value;
                            $(this).prop("checked", check);
                        } else {
                            $(this).val(value);
                        }
                        $(this).change();
                    }
                });
            },
            error: function (err) {
                document.write(err.responseText);
            }
        });
        _call('afterFillData');
    };
    /**
     * 判断值是否在数组中
     * @param {type} value
     * @param {type} array
     * @returns {Boolean}
     */
    this.inArray = function (value, array) {
        for (var i in array) {
            if (array[i] == value) {
                return true;
            }
        }
        return false;
    };
    /**
     * 获取多维数组的值
     * @param {type} array
     * @param {type} key
     * @returns {Array|OAForm.arrayPluck.response}
     */
    this.arrayPluck = function (array, key) {
        var response = [];
        for (var i in array) {
            try {
                var value = eval('array[i]' + key);
            } catch (err) {
                continue;
            }
            response.push(value);
        }
        return response;
    };
    /**
     * AJAX提交表单
     * @returns {Boolean} false
     */
    this.submitByAjax = function () {
        oaWaiting.show();
        self.tooltip.start();
        var url = $(this).attr("action");
        var data = $(this).serialize();
        var type = $(this).attr("method");
        $.ajax({
            type: type,
            url: url,
            data: data,
            async: false,
            dataType: 'json',
            success: function (msg) {
                if (msg['status'] === 1) {
                    _call('submitSuccess', [msg['message']]);
                } else if (msg['status'] === -1) {
                    _call('submitFail', [msg['message']]);
                }
            },
            error: function (err) {
                if (err.status === 422) {
                    var msg = err.responseJSON;
                    _call('validateError', [msg]);
                } else if (err.status === 403) {
                    var msg = err.responseText;
                    alert(msg);
                } else {
                    document.write(err.responseText);
                }
            }
        });
        oaWaiting.hide();
        return false;
    };
    /*
     * 验证提示
     */
    this.tooltip = new Tooltip(self.query);
    /*
     * 触发回调函数
     * @param {type} funName 函数名称
     * @returns {unresolved}
     */
    function _call(funName) {
        var params = arguments[1] ? arguments[1] : [];
        var paramText = '';
        for (var i in params) {
            paramText += 'params[' + i + '],';
        }
        paramText += 'self';
        self.dom.o = eval('self.setting.callback.' + funName);
        return eval('self.dom.o(' + paramText + ')');
    }

    /**
     * 触发初始化方法
     */
    this._construct();
}

/**
 * 表单列表
 * @param {type} dom
 * @param {type} options
 * @returns {FormList}
 */
function OAFormList(dom, options) {
    var self = this;
    var optionOrigin = {
        min: 1,
        max: 9999,
        clearEmpty: true,
        emptyInput: true,
        outerBtn: {
            addBtn: true
        },
        unit: {
            addBtn: true,
            deleteBtn: true
        },
        callback: {
            afterReset: function () {},
            afterAdd: function () {}
        }
    };
    this.setting = $.extend(true, {}, optionOrigin, options);
    this.dom = dom;
    this.query = $(this.dom);
    this.html = {};

    this._construct = function () {
        self.makeDom();
        self.query.replaceWith(self.box);
        self.dom = self.ul[0];
        self.reset();
        self.addEventListener();
        self.setting.emptyInput !== false && self.addEmptyInput();
    };
    /**
     * 绑定事件
     * @returns {undefined}
     */
    this.addEventListener = function () {
        self.ul.closest('form').on('submit', function () {
            self.setting.clearEmpty && self.removeEmptyGroup();
            self.setNameByGroup();
        });
    };
    /**
     * 添加空值input
     * @returns {undefined}
     */
    this.addEmptyInput = function () {
        var attributes = {};
        if (typeof self.setting.emptyInput === 'object') {
            attributes = self.setting.emptyInput;
        } else {
            var keyNames = self.getKeyNames();
            $.each(keyNames, function () {
                attributes[this] = '';
            });
        }
        for (var i in attributes) {
            self.html.emptyInput.clone().attr({'name': i, 'value': attributes[i]}).prependTo(self.box);
        }
    };
    /**
     * 重置
     * @returns {OAFormList}
     */
    this.reset = function () {
        var oldLi = self.ul.children();
        oldLi.remove();
        self.add(null, self.setting.min);
        _call('afterReset');
        return self;
    };
    /**
     * 在下方插入单元
     * @param {jQuery} li
     * @param {int} repeat
     * @returns {Boolean}
     */
    this.add = function () {
        var li = arguments[0] instanceof jQuery ? arguments[0] : null;
        var repeat = !isNaN(arguments[1]) ? arguments[1] : 1;
        if (repeat > 0 && self.ul.children().length < self.setting.max) {
            var newLi = self.html.li.clone(true);
            if (li) {
                li.after(newLi);
            } else {
                self.ul.prepend(newLi);
            }
            var newAddBtn = newLi.find('[addBtn]');
            newAddBtn.on('click', function () {
                self.add(newLi);
            });
            var newDeleteBtn = newLi.find('[deleteBtn]');
            newDeleteBtn.on('click', function () {
                self.delete(newLi);
            });
            self.toggleBtnDisabled();
            _call('afterAdd', [newLi]);
            self.add(li, repeat - 1);
        } else {
            return false;
        }
    };
    /**
     * 删除单元
     * @param {type} li
     * @returns {Boolean}
     */
    this.delete = function (li) {
        if (self.ul.children().length <= self.setting.min) {
            return false;
        }
        li.remove();
        self.toggleBtnDisabled();
    };

    this.makeDom = function () {
        self.box = $('<div>').addClass('oaFormList-box');
        self.ul = $('<div>').addClass('oaFormList-list').appendTo(self.box);
        self.html.emptyInput = $('<input>').attr({type: 'hidden', locked: true});
        self.html.addBtn = $('<button>').attr({type: 'button', title: '在下方插入', addBtn: ''}).addClass('btn btn-xs btn-success').html($('<i>').addClass('fa fa-plus'));
        self.html.deleteBtn = $('<button>').attr({type: 'button', title: '删除', deleteBtn: ''}).addClass('btn btn-xs btn-danger').html($('<i>').addClass('fa fa-times'));

        self.html.outerBtn = $('<p>').addClass('oaFormList-button text-center form-control-static').prependTo(self.box);
        self.setting.outerBtn.addBtn && self.html.outerBtn.append(self.html.addBtn.clone().on('click', self.add));

        self.html.btnGroup = $('<div>').css({position: 'absolute', right: '10px', top: '50%', 'margin-top': '-11px', 'z-index': 100}).attr({btnGroup: ''}).addClass('text-right').html(' ');
        self.setting.unit.addBtn && self.html.btnGroup.append(self.html.addBtn.clone().after(' '));
        self.setting.unit.deleteBtn && self.html.btnGroup.append(self.html.deleteBtn.clone());

        self.html.li = self.query.css({position: 'relative'}).prepend(self.html.btnGroup.clone());
    };
    /**
     * 设置按钮是否可用
     * @returns {undefined}
     */
    this.toggleBtnDisabled = function () {
        var liNum = self.ul.children().length;
        var deleteBtn = self.box.find('[deleteBtn]');
        if (liNum <= self.setting.min) {
            deleteBtn.addClass('disabled');
        } else {
            deleteBtn.removeClass('disabled');
        }
        var addBtn = self.box.find('[addBtn]');
        if (liNum >= self.setting.max) {
            addBtn.addClass('disabled');
        } else {
            addBtn.removeClass('disabled');
        }
    };
    /**
     * 在name中加入分组序号
     * @returns {undefined}
     */
    this.setNameByGroup = function () {
        self.ul.children().each(function (index) {
            $(this).find('input,select,textarea').each(function () {
                var name = $(this).attr('name').replace(/\[\d*\]/, '[' + index + ']');
                $(this).attr('name', name);
            });
        });
    };
    /**
     * 去除列表中的空白单元
     * @returns {undefined}
     */
    this.removeEmptyGroup = function () {
        self.ul.children().each(function (index) {
            var empty = true;
            var originLi = self.html.li.clone(true);
            $(this).find('input,select,textarea').each(function () {
                var originName = $(this).attr('name').replace(/(\[)\d*(\])/g, '$1$2');
                if ($(this).val() !== originLi.find('[name="' + originName + '"]').val()) {
                    empty = false;
                }
            });
            if (empty) {
                self.delete($(this));
            }
        });
    };
    /**
     * 渲染数据
     * @param {type} msg
     * @returns {undefined}
     */
    this.fillData = function (msg) {
        var keyNames = self.getKeyNames();
        var maxLength = 0;
        $.each(keyNames, function () {
            var length = msg[this] ? msg[this].length : 0;
            maxLength = length > maxLength ? length : maxLength;
        });
        self.add(null, maxLength - self.setting.min);
        self.setNameByGroup();
    };
    /**
     * 获取集合的键名
     * @returns {Window.keyNames|OAFormList.keyNames}
     */
    this.getKeyNames = function () {
        if (self.keyNames === undefined) {
            var keyNames = [];
            self.html.li.find('input,textarea,select').each(function () {
                var keyName = $(this).attr('name').replace(/\[\].*$/, '');
                if ($.inArray(keyName, keyNames) === -1) {
                    keyNames.push(keyName);
                }
            });
            self.keyNames = keyNames;
        }
        return self.keyNames;
    };
    /**
     * 触发回调函数
     * @param {type} funName 函数名称
     * @returns {unresolved}
     */
    function _call(funName) {
        var params = arguments[1] ? arguments[1] : [];
        var paramText = '';
        for (var i in params) {
            paramText += 'params[' + i + '],';
        }
        paramText += 'self';
        self.dom.o = eval('self.setting.callback.' + funName);
        return eval('self.dom.o(' + paramText + ')');
    }

    this._construct();
}

/**
 * 表单提交后的错误提示
 * @param {type} form
 * @returns {Tooltip}
 */
function Tooltip(form) {
    this.tooltipClass = "validity-tooltip";
    /**
     * 初始化
     * @returns {undefined}
     */
    this.start = function () {
        form.find("." + this.tooltipClass).remove();
    };
    /**
     * 生成并展示提示信息
     * @param {type} $obj
     * @param {type} msg
     * @returns {undefined}
     */
    this.raise = function ($obj, msg) {
        var pos = $obj.offset();
        pos.left = $obj.width() + parseInt($obj.css('padding-left')) + parseInt($obj.css('padding-right')) + 18;
        pos.top = ($obj.height() + parseInt($obj.css('padding-top')) + parseInt($obj.css('padding-bottom'))) / 2 - 11;
        var tooltip = $(
                '<div class="' + this.tooltipClass + '">' +
                '<div class="' + this.tooltipClass + '-arrow"></div>' +
                '<div class="' + this.tooltipClass + '-inner">' + msg + '</div>' +
                '</div>'
                )
                .click(function () {
                    $obj.focus();
                    $(this).fadeOut().remove();
                })
                .css(pos)
                .hide()
                .appendTo($obj.parent())
                .fadeIn();
        $obj.on("focus", function () {
            tooltip.fadeOut().remove();
        });
    };
    /**
     * 展示提示信息
     * @param {type} msg
     * @returns {undefined}
     */
    this.raiseAll = function (msg) {
        var alertText = '';
        for (var i in msg) {
            var value = msg[i][0];
            var key = i.replace(/\.(\w+)/g, '[$1]');
            var input = form.find('[name="' + key + '"]');
            if (input.length > 0 && value.length > 0) {
                this.raise(input, value);
            } else {
                alertText += value + ';';
            }
        }
        if (alertText.length > 0) {
            alert(alertText.trim(';'));
        }
    };
}

/**
 * 搜索
 * @param {type} dom
 * @param {type} options
 * @returns {OASearch}
 */
function OASearch(dom, options) {
    var self = this;
    this.dom = dom;
    this.query = $(dom);
    this.isShown = false;
    this.modal;
    this.table;
    var optionOrigin = {
        url: '',
        columns: [],
        params: [],
        target: [],
        themes: {
            staff: {
                url: '/hr/staff/list',
                columns: [
                    {data: "staff_sn", title: "编号"},
                    {data: "realname", title: "姓名"},
                    {data: "brand.name", title: "品牌", searchable: false},
                    {data: "department.full_name", title: "部门全称", searchable: false},
                    {data: "shop.name", title: "所属店铺", visible: false, searchable: false, defaultContent: ""},
                    {data: "position.name", title: "职位", searchable: false},
                    {data: "status.name", title: "状态", searchable: false}
                ]
            },
            all_staff: {
                url: '/hr/staff/list',
                params: {with_auth: false},
                columns: [
                    {data: "staff_sn", title: "编号"},
                    {data: "realname", title: "姓名"},
                    {data: "brand.name", title: "品牌", searchable: false},
                    {data: "department.full_name", title: "部门全称", searchable: false},
                    {data: "shop.name", title: "所属店铺", visible: false, searchable: false, defaultContent: ""},
                    {data: "position.name", title: "职位", searchable: false},
                    {data: "status.name", title: "状态", searchable: false}
                ]
            },
            shop: {
                url: "/hr/shop/list",
                columns: [
                    {data: "shop_sn", title: "店铺编号"},
                    {data: "name", title: "店铺名称"},
                    {data: "brand.name", title: "所属品牌", searchable: false},
                    {data: "manager_name", title: "店长"},
                    {data: "department.full_name", title: "所属部门", searchable: false},
                    {data: "province.name", title: "店铺地址(省)", visible: false, searchable: false, defaultContent: ""},
                    {data: "city.name", title: "店铺地址（市）", visible: false, searchable: false, defaultContent: ""},
                    {data: "county.name", title: "店铺地址（区）", visible: false, searchable: false, defaultContent: ""},
                    {data: "address", title: "店铺地址", sortable: false, searchable: false,
                        createdCell: function (nTd, sData, oData, iRow, iCol) {
                            var provinceName = oData.province ? oData.province.name + "-" : "";
                            var cityName = oData.city ? oData.city.name + "-" : "";
                            var countyName = oData.county ? oData.county.name : "";
                            var html = provinceName + cityName + countyName + " " + sData;
                            $(nTd).html(html);
                        }
                    }
                ]
            }
        }
    };
    this.setting = typeof options === 'string' ? optionOrigin.themes[options] : $.extend(true, {}, optionOrigin, options);
    this._construct = function () {
        var tagName = self.dom.tagName;
        switch (tagName) {
            case 'INPUT':
                self.query.on('click', self.show);
                self.setting.target = [self.query];
                break;
            default:
                self.query.find('[oaSearchShow]').on('click', self.show);
                self.setting.target = self.query.find('input');
                break;
        }
    };
    /**
     * 弹出搜索页面
     * @returns {undefined}
     */
    this.show = function () {
        if (!self.isShown) {
            var searchPage = self.makeSearchPage();
            searchPage.appendTo('body').modal('show');
            self.tableInit();
            self.isShown = true;
        }
    };
    /**
     * 隐藏并删除搜索页面
     * @returns {undefined}
     */
    this.hide = function () {
        self.modal.remove();
        self.isShown = false;
    };
    this.tableInit = function () {
        self.table.oaTable({
            columns: self.setting.columns,
            ajax: {
                url: self.setting.url,
                data: function (d) {
                    return $.extend({}, d, self.setting.params);
                }
            },
            scrollX: 570,
            scrollY: 900,
            pageLength: 20,
            lengthChange: false,
            pagingType: "numbers",
            stateSave: false,
            createdRow: function (row, data, dataIndex) {
                $(row).on('click', function () {
                    self.select(data);
                    self.modal.modal('hide');
                });
            }
        });
    };
    /**
     * 生成搜索页面
     * @returns {jQuery|OASearch.makeSearchPage.modal}
     */
    this.makeSearchPage = function () {
        var modal = self.modal = $('<div>').addClass('modal fade').on('hidden.bs.modal', self.hide);
        var modalDialog = $('<div>').addClass('modal-dialog').appendTo(modal);
        var modalContent = $('<div>').addClass('panel').appendTo(modalDialog);
        var modalBody = $('<div>').addClass('panel-body').appendTo(modalContent);
        var modalTable = self.table = $('<table>').addClass('table table-sm table-hover table-bordered');
        modalTable.appendTo(modalBody);
        return modal;
    };
    /**
     * 选中搜索结果
     * @param {type} data
     * @returns {undefined}
     */
    this.select = function (data) {
        $.each(self.setting.target, function () {
            var column = $(this).attr('oaSearchColumn');
            if (column && column.length > 0) {
                $(this).val(data[column]);
            }
        });
    };
    this._construct();
}

function OATable(dom, options) {
    var self = this;
    this.dom = dom;
    this.query = $(dom);
    this._construct = function () {
        self.getButtonFromLibrary();
        if (self.setting.filter === true) {
            self.makeFilter();
            self.setting.buttons.unshift(self.buttonLibrary.filter);
        } else if (self.setting.filter instanceof jQuery) {
            self.getFilter();
            self.setting.buttons.unshift(self.buttonLibrary.filter);
        }
        self.setting.buttons.unshift(self.buttonLibrary.colvis, self.buttonLibrary.reload);
        $.extend(this, this.query.DataTable(this.setting));
    };
    var optionOrigin = {
        columns: [
//            {className: 'select-checkbox', orderable: false, searchable: false, defaultContent: ''}
        ],
        ajax: {url: "", type: "POST", data: {}},
        scrollX: 1015,
        deferRender: true,
        processing: true,
        scrollCollapse: true,
        serverSide: true,
        searchDelay: 500,
        stateSave: true,
        responsive: false,
        colReorder: false,
        autoWidth: false,
        searching: true,
        filter: false,
//        select: {
//            style: 'nulti',
//            selector: 'td:first-child'
//        },
        language: {
            lengthMenu: "每页显示 _MENU_ 项",
            search: "搜索",
            paginate: {
                first: "首页",
                last: "末页",
                next: "下一页",
                previous: "上一页"
            },
            info: "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
            infoEmpty: "显示第 0 至 0 项结果，共 0 项",
            infoFiltered: "(由 _MAX_ 项结果过滤)",
            processing: "处理中...",
            emptyTable: "数据为空",
            zeroRecords: "没有匹配的结果"
        },
        dom: "<'row bg-warning oaTableFilter hide'<'col-sm-12'>><'row'<'col-sm-3'l><'col-sm-6'<'dataTables_length'B>><'col-sm-3'f>><'row'<'col-sm-12't>>r<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [],
        stateSaveParams: function (settings, data) {
            data.filter = settings.ajax.data.filter;
        },
        stateLoadParams: function (settings, data) {
            if (data.filter !== undefined) {
                for (var name in  data.filter) {
                    var value = data.filter[name];
                    self.filterBox.find("[name='" + name + "']").val(value);
                }
                settings.ajax.data.filter = data.filter;
            }
        },
        initComplete: function (settings, json) {
            for (var i in settings.ajax.data.filter) {
                self.buttons().container().find('.fa-filter').css("color", "#65cea7");
                break;
            }
        }
    };
    this.setting = $.extend(true, {}, optionOrigin, options);
    this.buttonLibrary = {
        colvis: {extend: "colvis", text: "<i class='fa fa-eye-slash fa-fw'></i>", titleAttr: "可见字段", className: "btn-primary"},
        reload: {text: "<i class='fa fa-refresh fa-fw'></i>", titleAttr: "刷新", className: "btn-primary", action: function (e, dt, node, config) {
                dt.draw();
            }
        },
        filter: {text: "<i class='fa fa-filter fa-fw'></i>", titleAttr: "筛选", className: "btn-primary", action: function () {
                self.filterBox.slideToggle();
            }
        },
        export: function (url) {
            return {text: "<i class='fa fa-download fa-fw'></i>", titleAttr: "导出", className: "btn-default", action: function () {
                    self.exportToExcel(url);
                }
            };
        }
    };
    /**
     * 根据配置生成筛选界面
     * @returns {undefined}
     */
    this.makeFilter = function () {
        self.filterBox = $('<div>').addClass('oaTableFilter').slideUp().insertBefore(self.query);
        self.filterForm = $('<form>').appendTo(self.filterBox).html('test Filter');
        $.each(self.setting.columns, function (key, column) {
            console.log(column);
        });
        self.filterForm.on('submit', self.submitFilter);
    };
    /**
     * 绑定已有筛选界面
     * @returns {undefined}
     */
    this.getFilter = function () {
        self.filterBox = self.setting.filter;
        self.filterForm = self.filterBox.find('form');
        self.filterForm.find('[isDate][isDate!=false]').oaDate();
        self.filterForm.find('[isDateTime][isDateTime!=false]').oaDateTime();
        self.filterForm.find('[oaSearch][oaSearch!=false]').each(function () {
            $(this).oaSearch($(this).attr('oaSearch'));
        });
        self.filterForm.on('submit', self.submitFilter);
    };
    this.submitFilter = function () {
        var info = self.filterForm.serializeArray();
        var condition = false;
        var filter = {};
        for (var i in info) {
            var v = info[i];
            if (v.value.length > 0) {
                condition = true;
                var filterName = v.name;
                if (typeof filter[filterName] === undefined) {
                    filter[filterName] = [];
                }
                if (v.name.search('[]') > 0) {
                    filter[filterName].push(v.value);
                } else {
                    filter[filterName] = v.value;
                }
            }
        }
        self.settings()[0].ajax.data.filter = filter;
        self.draw();
        if (condition) {
            self.buttons().container().find('.fa-filter').css("color", "#65cea7");
        } else {
            self.buttons().container().find('.fa-filter').attr("style", false);
        }
        return false;
    };
    /**
     * 导出为excel
     * @returns {undefined}
     */
    this.exportToExcel = function (url) {
        var dataCount = self.ajax.json().recordsFiltered;
        if (dataCount == 0) {
            alert("无可用信息");
        } else if (confirm("确认以当前条件导出？")) {
            oaWaiting.show();
            var params = self.ajax.params();
            delete params.length;
            $.ajax({
                type: "POST",
                url: url,
                data: params,
                dataType: 'json',
                success: function (msg) {
                    if (msg['state'] == 1) {
                        var fileName = msg['file_name'];
                        window.location.href = '/storage/exports/' + fileName + '.xlsx';
                        oaWaiting.hide();
                    }
                },
                error: function (err) {
                    document.write(err.responseText);
                }
            });
        }
    };

    this.getButtonFromLibrary = function () {
        for (var i in self.setting.buttons) {
            var button = self.setting.buttons[i];
            if (typeof button === 'string') {
                var buttonName = button.match(/^(\w+):?(.*)$/)[1];
                var buttonParams = button.match(/^(\w+):?(.*)$/)[2].split(',');
                if (typeof self.buttonLibrary[buttonName] === 'object') {
                    self.setting.buttons[i] = self.buttonLibrary[buttonName];
                } else if (typeof self.buttonLibrary[buttonName] === 'function') {
                    self.setting.buttons[i] = self.buttonLibrary[buttonName].apply(self, buttonParams);
                }
            }
        }
    };
    this._construct();
}
