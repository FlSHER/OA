/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//列表控件
var listCtrlData = {
    //初始化序号
    init_sort: function (tbody) {
        $.each(tbody.find('tr'), function (k, v) {
            if (tbody.find('tr').hasClass('hiddens')) {//去除合计排序
                if (k < tbody.find('tr').length - 1) {
                    $(this).find('td:first').text(k + 1);
                    listCtrlData.init_name($(this), k);
                }
            } else {
                $(this).find('td:first').text(k + 1);
                listCtrlData.init_name($(this), k);
            }
        });
    },
    //初始name值
    init_name: function (this_tr, tr_k) {
        $.each(this_tr.find('td'), function (k, v) {
            if (k > 0 && k < this_tr.find('td').length - 1) {//去除 序号和操作
                var name = $(this).children().attr('name');//获取当前td的name值
                var new_name = name.split('_');
                new_name = new_name[0] + '_' + tr_k;
                $(this).children().attr('name', new_name);
            }
        });
    },
    //新增
    add: function (_this) {
        //添加单行与多行处理
        this.add.get_add_html = function (tr_html, add_row) {
            var html = '';
            var tr_k = o_tbody.find('tr').length - 1;
            for (var i = 1; i <= parseInt(add_row); i++) {
                var html_str = tr_html.replace(/_x"/g, '_' + (tr_k + i) + '"');
                html += html_str;
            }
            return html;
        };


        var add_row = $(_this).next('.add_row').val();//行数
        if (add_row == '' || add_row == 0) {
            return false;
        }
        var o_tbody = $(_this).parent().prev('table').find('tbody');
        var tr_html = $(_this).parent().nextAll('.add_tr').text();

        var html = this.add.get_add_html(tr_html, add_row);//tr的html

        if (o_tbody.find('tr').hasClass('hiddens')) {
            o_tbody.find('.hiddens').before(html);
            this.total(o_tbody.find('.hiddens').find('td span'));//触发合计
        } else {
            o_tbody.append(html);
        }

        this.init_sort(o_tbody);//初始化排序

    },
    //删除
    del: function (_this) {
        var o_tbody = $(_this).parents('tbody');
        $(_this).parents('tr').remove();
        this.init_sort(o_tbody);//序号重新排序

        if (o_tbody.find('tr').hasClass('hiddens')) {
            this.total(o_tbody.find('.hiddens').find('td span'));//触发合计
        }
    },
    /*
     * 选择
     * id 数据库的database_manage的id
     * db_table_name 数据表名
     * fields 数据字段
     * self 当前点击的index 索引
     * table_id table表格的id
     * @param {type} str
     * @returns {unresolved}
     */
    optionalField: function (id, db_table_name, fields, self, table_id) {
        var index = $(self).parents('tr').index();
        var fields_arr = fields.split('`');
        var newArr = new Array();
        for (var i = 0; i < fields_arr.length - 1; i++) {
            if (fields_arr[i] != '0') {
                newArr.push(fields_arr[i]);
            }
        }
        var url = '/workflow/optionalFieldList?id=' + id + '&table=' + db_table_name + '&fields_arr=' + newArr + '&index=' + index + '&table_id=' + table_id;
        window.open(url, 'my', 'height=600, width=1000, left=200,top=100, toolbar=no, menubar=no, resizable=no, location=no, status=no');

    },
    //合计
    total: function (_this) {
        var orgsum = $(_this).parents('tbody').parent('table').attr('orgsum');
        orgsum = orgsum.replace(/`$/, '');
        var orgsumArr = orgsum.split('`');
        $.each(orgsumArr, function (i, v) {
            if (1 == v) {
                var trAll = $(_this).parents('tbody').find('tr');
                var total = 0;
                $.each(trAll, function (k, v) {
                    if (k < trAll.length - 1) {
                        var td = $(this).find('td').eq(i + 1).children().val();
                        td = (td == '') ? 0 : td;
                        total += parseFloat(td);
                    }
                });
                $(_this).parents('.hiddens').find('td').eq(i + 1).text(total);
            }
        });
    },
    /*----------------------------计算start------------------------------*/
    //计算
    calculate: function (id) {
        var tr = $('#' + id).find('tbody tr').not('.hiddens');
        var equation = $('#' + id).attr('equation');//计算公式
        var equationArr = equation.split('`');
        $.each(tr, function () {
            listCtrlData.evalValue($(this), equationArr);//前tr计算的值
        });

        var _this = $('#' + id).find('tbody .hiddens').find('td span');
        listCtrlData.total(_this);//触发合计
    },
    //当前tr计算的值
    evalValue: function (_this, equationArr) {
        var valueGroup = new Array();//tr 下所有td计算的值
        $.each(equationArr, function (i, v) {
            if (v.length > 0) {
                var code = v.replace(/\[(\d+)\]/g, 'Number(_this.find("td").eq($1).children().val())');
                var value = eval(code);//当前td计算的值
                valueGroup[i + 1] = value;
            }
        });
        for(var i in valueGroup){
            var v = valueGroup[i];
            _this.find('td').eq(i).children().val(v);
        }
    },
    /*-------------------------------------计算end--------------------------------------------*/
};


