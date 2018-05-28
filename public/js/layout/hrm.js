/**
 * 搜索员工
 * @param {object} options
 * @returns {StaffSearcher}
 */
var StaffSearcher = function (options) {
    this.options = options;

    /**
     * 从options获取传值目标
     */
    this.targets = function () {
        var targetsOrigin = this.options.targets;
        var targets = [];
        if (typeof targetsOrigin == 'string') {
            targets = [{from: 'staff_sn', to: targetsOrigin}];
        } else if (typeof targetsOrigin == 'object') {
            for (var i in targetsOrigin) {
                var targetTmp = targetsOrigin[i];
                if (typeof targetTmp == 'string') {
                    targets.push({from: i, to: targetTmp});
                } else if (typeof targetTmp == 'object') {
                    targets.push({from: targetTmp['from'], to: targetTmp['to']});
                }
            }
        }
        return targets;
    };

    /**
     * ajax参数
     */
    this.params = {
        targets: this.targets
    };

    /**
     * 单一标识
     * @returns {string}
     */
    this.mark = '_' + parseInt($.now() / 1000);

    /**
     * 判断是否为input框
     */
    this.isInput = this.is('input');

    /**
     * 判断是否为按钮
     */
    this.isBtn = this.is('button') || this.is('a');


    /**
     * 绑定事件
     */
    this.attachEvents = function () {
        
    };

    /**
     * 弹出搜索窗口
     */
    this.show = function () {
        var url = "/hr/staff/search";
        $.ajax({
            type: "POST",
            url: url,
            data: this.params,
            dataType: 'json',
            success: function (msg) {
                $("body").append(msg);
                $("#openSearchStaffResult").click();
            },
            error: function (err) {
                document.write(err.responseText);
            }
        });
    };
    /**
     * 关闭搜索框
     */
    this.close = function () {

    };

    return this;
};


// 搜索员工初始化
$.fn.staffSearcher = StaffSearcher;