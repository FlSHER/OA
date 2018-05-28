<!--触发器-->
<!--steps_unit-->
<!--<div class="tab-pane" id="unit">
    <div id="in_flow_top_type" class="flow_top_type clearfix">
        <div style="float:left"><span class="icon20-unit_front">管理触发器</span></div>
        <div style="float:right;margin-top:5px;margin-right:5px;"></div>
        <button type="button" id="in_condition" class="btn btn-primary" style="float: right; margin-bottom:2px" onclick="javascript: loadModal('newTriggerModal')">新建触发器</button>
    </div>
    <div id="in_circulation_sponsor" class="circulation_sponsor" style="clear:both;">
        <div class="condition_sponsor_table">
            <table class="table table-bordered" id="prcs_in_tab_plugin">
                <thead>
                    <tr class="condition_sponsor_table_tr">
                        <td id="condition_sponsor_table_td6" style="color:black;font-weight:bold;">触发节点</td>
                        <td id="condition_sponsor_table_td1" style="color:black;font-weight:bold;">排序号</td>
                        <td id="condition_sponsor_table_td4" style="color:black;font-weight:bold;">名称</td>
                        <td id="condition_sponsor_table_td4" style="color:black;font-weight:bold;">执行插件</td>
                        <td id="condition_sponsor_table_td2" style="color:black;font-weight:bold;">执行方式</td>
                        <td id="condition_sponsor_table_td2" style="color:black;font-weight:bold;">触发器描述</td>
                        <td id="condition_sponsor_table_td2" style="color:black;font-weight:bold;">操作</td>
                    </tr>
                </thead>
                <tbody id="condition_in_plugin">
                </tbody>
            </table>
            <input type="hidden" name="triggers_str" id="triggers_str" value="">
        </div>
        <div class="alert fade in condition_warning" style="color: red;padding-left:0px; width: 785px;">
            <button type="button" class="close" data-dismiss="alert">×</button>
            说明：列表中按照 【新建操作】-&gt;【接收操作】-&gt;【保存操作】-&gt;【转交操作】-&gt;【委托操作】-&gt;【退回操作】 的顺序呈现<br>同时，同一触发节点通过 [排序号] 控制触发顺序    </div>
    </div>
    -- 新建触发器窗口 --- 
    <div id="newTriggerModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="font-size: 14px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <div id="trigger_modal_header_div" style="display:none;">
            </div>
            <h3 id="trigger_myModalLabel" style="display:block;white-space:nowrap;overflow:hidden;font-size: 25px;font-weight: bold;">新建触发器</h3>
        </div>
        <div class="modal-body" id="trigger_myModal_intrust">

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="javascript: putTriggerInfor(this, 'trigger_table');">确定</button>
            <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        </div>
    </div>
    -- 编辑触发器窗口 --- 
    <div id="TriggerEditModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="font-size: 14px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <div id="trigger_edit_modal_header_div" style="display:none;">
            </div>
            <h3 id="trigger_myModalLabel" style="display:block;white-space:nowrap;overflow:hidden;font-size: 25px;font-weight: bold;">编辑触发器</h3>
        </div>
        <div class="modal-body" id="trigger_edit_myModal_intrust">
            <p>加载中...</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="javascript: putTriggerInfor(this, 'trigger_edit_table');">确定</button>
            <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        </div>
    </div>

    {{-- <script type="text/javascript" src="/static/js/jquery-1.5.1/jsrender/jsrender.min.js"></script>

    <script type="text/javascript">

                function show_span(obj) {
                    var value = jQuery(obj).val();
                    var parent = jQuery(obj).parent();
                    parent.html('');
                    parent.html('<span title="' + value + '">' + value + '</span>');
                }
    </script>
    <script>
        jQuery(document).ready(function () {
            jQuery(window).resize(function () {
                jQuery('.modal-body').css('max-height', jQuery(window).height() - 200);
            });
        });
        //弹出窗口
        function loadModal(loadType)
        {
            if (loadType == 'newTriggerModal')
            {
                var template = jQuery.templates("#trigger_newTmpl");
                var htmlOutput = template.render({});
                jQuery("#trigger_myModal_intrust").html(htmlOutput);

                jQuery('#newTriggerModal').modal({
                    keyboard: true,
                    backdrop: "static"
                });
                jQuery('#newTriggerModal').css('top', '20px');
                jQuery('#newTriggerModal').find('.modal-body').css('max-height', jQuery(window).height() - 200);
                jQuery('#newTriggerModal').find('.modal-footer').css('padding', '8px 15px 8px');
            } else if (loadType == 'TriggerEditModal')
            {
                jQuery('#TriggerEditModal').modal({
                    keyboard: true,
                    backdrop: "static"
                });
                jQuery('#TriggerEditModal').css('top', '20px');
                jQuery('#TriggerEditModal').find('.modal-body').css('max-height', jQuery(window).height() - 200);
                jQuery('#TriggerEditModal').find('.modal-footer').css('padding', '8px 15px 8px');
            }
        }

        function sel_plugin(obj_name)
        {
            var URL = "sel_plugin.php?name=" + obj_name;
            var loc_y = loc_x = 200;
            if (is_ie)
            {
                loc_x = document.body.scrollLeft + event.clientX - event.offsetX;
                loc_y = document.body.scrollTop + event.clientY - event.offsetY;
            }
            LoadDialogWindow(URL, self, loc_x, loc_y, 1150, 420);
        }
        function remove_plugin(id)
        {
            document.getElementById(id).value = "";
            //document.form1.PLUGIN.value="";
        }
        function upd_plugin()
        {
            document.form1.PLUGIN.value = plugin_file;
        }
        function LoadDialogWindow(URL, parent, loc_x, loc_y, width, height)
        {
            if (window.showModalDialog)//window.open(URL);
                window.showModalDialog(URL, parent, "edge:raised;scroll:1;status:0;help:0;resizable:1;dialogWidth:" + width + "px;dialogHeight:" + height + "px;dialogTop:" + loc_y + "px;dialogLeft:" + loc_x + "px", true);
            else
                window.open(URL, "load_dialog_win", "height=" + height + ",width=" + width + ",status=0,toolbar=no,menubar=no,location=no,scrollbars=yes,top=" + loc_y + ",left=" + loc_x + ",resizable=yes,modal=yes,dependent=yes,dialog=yes,minimizable=no", true);
        }
        // 获得触发器信息，用于编辑 DJ
        function getTriggerInfor(triggerId)
        {
            var td_objs = jQuery('#' + triggerId).find('td');
            var triggerInfor = {};
            triggerInfor.PLUGIN_TYPE = td_objs.eq(0).attr('plugin_type');
            triggerInfor.SORT_ID = td_objs.eq(1).html();
            triggerInfor.unitcsName = td_objs.eq(2).html();
            triggerInfor.PLUGIN = td_objs.eq(3).html();
            triggerInfor.REAL_PLUGIN = td_objs.eq(3).attr('real_plugin');
            triggerInfor.PLUGIN_WAY = td_objs.eq(4).attr('plugin_way');
            triggerInfor.DESCRIPTION = td_objs.eq(5).html();
            triggerInfor.ACTIVED = td_objs.eq(6).find('input:checked').length;
            triggerInfor.id = jQuery('#' + triggerId).attr('id');
            triggerInfor.TRIGGER_ID = jQuery('#' + triggerId).attr('trigger_id');


            var template = jQuery.templates("#trigger_editTmpl");
            var htmlOutput = template.render(triggerInfor);
            jQuery("#trigger_edit_myModal_intrust").html(htmlOutput);
            loadModal("TriggerEditModal");
        }

        // 将触发器信息返回到管理列表 DJ
        function putTriggerInfor(obj, tableName)
        {
            // 使IE8 数组 支持indexOf方法
            can_indexOf();

            var tr_objs = jQuery('#' + tableName).find('tr');
            var Data = {};
            var return_flag = false;
            var plugin_type_arr = ["CREATE", "RECEIVE", "SAVE", "TURN", "INTRUST", "BACK"];
            var plugin_type_json = {"CREATE": "新建操作", "RECEIVE": "接收操作", "SAVE": "保存操作", "TURN": "转交操作", "INTRUST": "委托操作", "BACK": "退回操作"};
            var old_trigger_id = jQuery('#' + tableName).attr('old_trigger_id');

            Data.PLUGIN_TYPE = tr_objs.eq(0).find('select').val();
            Data.SORT_ID = jQuery.trim(tr_objs.eq(1).find('input').val());
            Data.unitcsName = jQuery.trim(HTMLEncode(tr_objs.eq(2).find('input').val()));
            Data.PLUGIN = tr_objs.eq(3).find('input').val();
            Data.REAL_PLUGIN = tr_objs.eq(3).find('input').attr('real_plugin');
            Data.PLUGIN_WAY = tr_objs.eq(4).find('select').val();
            Data.DESCRIPTION = jQuery.trim(HTMLEncode(tr_objs.eq(5).find('textarea').val()));
            Data.ACTIVED = tr_objs.eq(6).find('input:checked').val();
            Data.TRIGGER_ID = jQuery('#' + tableName).attr('trigger_id');

            // 校验数据
            if (typeof Data.SORT_ID == 'string')
            {
                var reg = /^\d+$/;
                if (!Data.SORT_ID.match(reg))
                {
                    alert('排序号只能为非负整数');
                    return_flag = true;
                    return false;
                }
            }
            jQuery.each(Data, function (key, val) {
                if (typeof val == 'undefined' || (jQuery.trim(val).match(/^(&nbsp;)*$/) && key != 'DESCRIPTION'))
                {
                    alert('除描述外，所有项为必填');
                    return_flag = true;
                    return false;
                }
            });
            if (!return_flag)
            {
                // 删除原有触发器信息
                jQuery('#' + old_trigger_id).remove();

                var triggerTableTrs = jQuery('#prcs_in_tab_plugin tr[plugin_type]');
                if (triggerTableTrs.length == 0)
                {
                    // 直接放在表格中
                    var htmlOfTr = buildTr(Data);
                    jQuery('#condition_in_plugin').prepend(htmlOfTr);
                } else
                {
                    // 先找相同节点类型的
                    var sameTypeTrigger = jQuery('#prcs_in_tab_plugin tr[plugin_type=' + Data.PLUGIN_TYPE + ']');
                    if (sameTypeTrigger.length != 0)
                    {
                        sameTypeTrigger.each(function (index, element) {
                            // 获得排序号用于排序
                            var plugin_type_sort_str = jQuery(element).attr('id');
                            var sort_id = parseInt(plugin_type_sort_str.split('__')[1]);

                            // 比较SORT_ID
                            if (Data.SORT_ID > sort_id)
                            {
                                // 最后一次，放在同类型最后
                                if (index == sameTypeTrigger.length - 1)
                                {
                                    var htmlOfTr = buildTr(Data);
                                    jQuery(element).after(htmlOfTr);
                                }
                                return true;
                            } else if (Data.SORT_ID <= sort_id)
                            {   // 放在上面
                                var htmlOfTr = buildTr(Data);
                                jQuery(element).before(htmlOfTr);
                                return false;
                            }
                        });
                    } else
                    {
                        // 遍历整个触发器列表
                        triggerTableTrs.each(function (index, element) {
                            // 获得节点类型用于排序

                            var plugin_type_sort_str = jQuery(element).attr('id');
                            var plugin_type = plugin_type_sort_str.split('__')[0];
                            var new_plugin_type_sort = plugin_type_arr.indexOf(Data.PLUGIN_TYPE);
                            var plugin_type_sort = plugin_type_arr.indexOf(plugin_type);

                            if (new_plugin_type_sort > plugin_type_sort)
                            {
                                // 最后一次，放在表格最后
                                if (index == triggerTableTrs.length - 1)
                                {
                                    var htmlOfTr = buildTr(Data);
                                    jQuery(element).after(htmlOfTr);
                                }
                                return true;
                            } else if (new_plugin_type_sort < plugin_type_sort)
                            {
                                // 放在前面
                                var htmlOfTr = buildTr(Data);
                                jQuery(element).before(htmlOfTr);
                                return false;
                            }
                        });
                    }
                }
                jQuery(obj).next().trigger('click');
            }

        }
        function buildTr(Data)
        {
            // 加入id 保证每一行的唯一性
            var id = jQuery('#condition_in_plugin tr').length + 1;
            var plugin_type_json = {"CREATE": "新建操作", "RECEIVE": "接收操作", "SAVE": "保存操作", "TURN": "转交操作", "INTRUST": "委托操作", "BACK": "退回操作"};
            var plugin_way_json = {"BEFORE_FRONTEND": "前台,执行操作前", "AFTER_FRONTEND": "前台,执行操作后", "BEFORE_BACKEND": "后台,执行操作前", "AFTER_BACKEND": "后台,执行操作后"};

            var htmlOfTr = "<tr id='" + Data.PLUGIN_TYPE + "__" + Data.SORT_ID + "__" + id + "' trigger_id='" + Data.TRIGGER_ID + "' plugin_type='" + Data.PLUGIN_TYPE + "'>";
            htmlOfTr += "<td plugin_type='" + Data.PLUGIN_TYPE + "' style='text-align:center; vertical-align:middle;'>" + plugin_type_json[Data.PLUGIN_TYPE] + "</td>";
            htmlOfTr += "<td style='text-align: center; vertical-align: middle;'>" + Data.SORT_ID + "</td>";
            htmlOfTr += "<td style='text-align: center; vertical-align: middle;'>" + Data.unitcsName + "</td>";
            htmlOfTr += "<td real_plugin='" + Data.REAL_PLUGIN + "' style='text-align: center; vertical-align: middle;'>" + Data.PLUGIN + "</td>";
            htmlOfTr += "<td real_plugin_way='" + Data.PLUGIN_WAY + "' plugin_way='" + Data.PLUGIN_WAY + "' style='text-align: center; vertical-align: middle;'>" + plugin_way_json[Data.PLUGIN_WAY] + "</td>";
            htmlOfTr += "<td style='text-align: center; max-width: 120px;'>" + Data.DESCRIPTION + "</td>";
            htmlOfTr += "<td style='text-align: center; width:auto; vertical-align: middle;'>";
            htmlOfTr += "<a href='javascript: void(0);' onclick=\"javascript: getTriggerInfor('" + Data.PLUGIN_TYPE + "__" + Data.SORT_ID + "__" + id + "');\">编辑</a> <a href='javascript: void(0);' onclick=\"javascript: delTriggerInfor('" + Data.PLUGIN_TYPE + "__" + Data.SORT_ID + "__" + id + "');\">删除</a> &nbsp;&nbsp;";
            htmlOfTr += "<span style='display: inline-block;text-align: center'>";
            htmlOfTr += "<input type='checkbox' style='margin:0px;' id='" + Data.PLUGIN_TYPE + "__" + Data.SORT_ID + "__" + id + "1' value='1'";
            if (Data.ACTIVED == 1)
            {
                htmlOfTr += " checked ";
            }
            htmlOfTr += ">";
            htmlOfTr += "<label style='display: inline;' for='" + Data.PLUGIN_TYPE + "__" + Data.SORT_ID + "__" + id + "1'>启用</label></td>";
            htmlOfTr += "</span>";
            htmlOfTr += "</tr>";

            return htmlOfTr;
        }
        window.del_trigger_ids = {};
        function delTriggerInfor(triggerId)
        {
            if (confirm('确定要删除吗'))
            {
                var trigger_id = jQuery('#' + triggerId).attr('trigger_id');
                window.del_trigger_ids[trigger_id] = trigger_id;
                jQuery('#' + triggerId).remove();
            }
        }
        // 去除不可用 执行方式，函数传的两个值 一个是为状态（接收呀，保存呀），另外一个为传过来的类型是new还是edit
        function change_plugin_type(plugin_type, control_type)
        {
            var CONTROL_TYPE = "PLUGIN_WAY_" + control_type;
            var BEFORE_FRONTEND = '<option value="BEFORE_FRONTEND" >前台,执行操作前</option>';
            var AFTER_FRONTEND = '<option value="AFTER_FRONTEND" >前台,执行操作后</option>';
            if (plugin_type == 'RECEIVE' || plugin_type == 'SAVE')
            {

                jQuery('#' + CONTROL_TYPE + '').find('option[value=BEFORE_FRONTEND]').remove();
                jQuery('#' + CONTROL_TYPE + '').find('option[value=AFTER_FRONTEND]').remove();
            } else
            {
                if (jQuery('#' + CONTROL_TYPE + '').find('option[value=AFTER_FRONTEND]').length == 0)
                {
                    jQuery('#' + CONTROL_TYPE + '').prepend(AFTER_FRONTEND);
                }
                if (jQuery('#' + CONTROL_TYPE + '').find('option[value=BEFORE_FRONTEND]').length == 0)
                {
                    jQuery('#' + CONTROL_TYPE + '').prepend(BEFORE_FRONTEND);
                }
            }
        }
        // html 转码
        function HTMLEncode(input)
        {
            var converter = document.createElement("DIV");
            if (jQuery) {
                jQuery(converter).text(input);
                var output = jQuery(converter).html();
            } else {
                converter.textContent = input;
                var output = converter.innerHTML;
            }
            converter = null;
            return output;
        }
        function HTMLDecode(input)
        {
            var converter = document.createElement("DIV");
            if (jQuery) {
                jQuery(converter).html(input);
                var output = jQuery(converter).text();
            } else {
                converter.innerHTML = input;
                var output = converter.innerText;
            }
            converter = null;
            return output;
        }
        //IE8 支持indexOf
        function can_indexOf()
        {
            if (!Array.prototype.indexOf)
            {
                Array.prototype.indexOf = function (elt /*, from*/)
                {
                    var len = this.length >>> 0;
                    var from = Number(arguments[1]) || 0;
                    from = (from < 0)
                            ? Math.ceil(from)
                            : Math.floor(from);
                    if (from < 0)
                        from += len;
                    for (; from < len; from++)
                    {
                        if (from in this &&
                                this[from] === elt)
                            return from;
                    }
                    return -1;
                };
            }
        }
    </script>		 --}}		
</div>-->