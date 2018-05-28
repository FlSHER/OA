<!--flow_data_page-->
<div id="data_content" style="width:60%;">
  <div class="accordion clearfix" style="width:100%;padding:15px;">
    <div class="bottom_info_right_top" style="margin-top:7px;font-size:16px;color:#000000;font-weight: bold;float:left;width:20%;" title="{{$data['flow_name']}}">{{$data['flow_name']}}</div>
    <div class="bottom_info_right_top1" style="float:right;color:#000000;width:348px;font-size:14px;display: none;color: ">该流程下共有 
      <span class="number-info">4</span> 个工作，其中 
      <span class="number-info">0</span> 个已删除               
      <button class="btn btn-info" style="margin-left:5px;margin-bottom:5px;" onclick="gotoCopyFlowUrl();">流程克隆</button>
    </div>
  </div>
  <div style="padding-left: 15px;padding-right: 15px;">
    <!--collapse start-->
    <div class="panel-group" id="accordion2">
      <div class="panel" style="border: 1px solid #cccccc;">
        <div class="panel-heading" style="background:#F7F7F7;">
          <h4 class="panel-title">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne2" onclick="rotateImag(this)">
              <img src="/css/workflow/images/unfold.png" style="padding-bottom:4px;">
              <b>设计流程</b>
            </a>
          </h4>
        </div>
        <div id="collapseOne2" class="panel-collapse collapse">
          <div class="panel-body">
            <div style="float:left;height:150px;font-size: 13px;">
              <div class="guide_definition" onclick="flowSkip('./flowAttribute?flow_id={{$data['flow_id']}}&skip=1')">
                <div class="icon64-guide_img_1" style="margin-top:8px;"></div>
                <div class="guide_title">
                  <div>
                    <a href="javascript:;" >定义流程属性</a>
                  </div>
                  <span style="color:#008ae7;">定义流程名称、流程类型、文号、流程说明等流程基本信息 </span>
                </div>
              </div>
              <div id="guide_type_fixed" class="guide_definition" onclick="flowSkip('./deviseFlowSteps?flow_id={{$data['flow_id']}}&flow_name={{$data['flow_name']}}&skip=1')">
                <div class="icon64-guide_img_2" style="margin-top:8px;"></div>
                <div class="guide_title">
                  <div>
                    <a href="javascript:;">设计流程步骤</a>
                  </div>
                  <span style="color:#37AEFF;">设计流程各步骤、经办权限、可写、必填和保密字段设置、转入转出条件设置。</span>
                </div>
              </div>
              <div class="guide_definition" onclick="flowSkip('{{asset(route('workflow.flowDesignPreview',['flow_id'=>$data['flow_id']]))}}')">
                <div class="icon64-guide_img_3" style="margin-top:8px;"></div>
                <div class="guide_title">
                  <div>
                      <a href="javascript:;">预览表单</a>
                  </div>
                  <span style="color:#73BD42;">查看表单样式 </span>
                </div>
              </div>
              <div class="guide_definition" onclick="window.parent.G.openURL('','导入流程','/general/system/workflow/flow_guide/flow_type/imp_xml.php?FLOW_ID=131')" style="display: none;">
                <div class="icon64-guide_img_4" style="margin-top:8px;"></div>
                <div class="guide_title">
                  <div>
                    <a href="javascript:;">导入</a>
                  </div>
                  <span style="color:#EF8400;">导入XML格式的流程定义文件 </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="panel" style="border: 1px solid #cccccc;">
        <div class="panel-heading" style="background: #F7F7F7;">
          <h4 class="panel-title">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo2" onclick="rotateImag(this)">
              <img src="/css/workflow/images/unfold.png" style="padding-bottom:4px;">
              <b>管理权限</b>
            </a>
          </h4>
        </div>
        <div id="collapseTwo2" class="panel-collapse collapse">
          <div class="panel-body">
            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
          </div>
        </div>
      </div>
      <div class="panel" style="border: 1px solid #cccccc;">
        <div class="panel-heading" style="background: #F7F7F7;">
          <h4 class="panel-title">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree2" onclick="rotateImag(this)">
              <img src="/css/workflow/images/unfold.png" style="padding-bottom:4px;">
              <b>其他</b>
            </a>
          </h4>
        </div>
        <div id="collapseThree2" class="panel-collapse collapse">
          <div class="panel-body">
            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
          </div>
        </div>
      </div>
    </div>
    <!--collapse end-->
  </div>
</div>