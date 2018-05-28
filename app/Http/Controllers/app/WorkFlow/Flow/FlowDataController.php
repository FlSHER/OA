<?php

namespace App\Http\Controllers\app\WorkFlow\Flow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;

use App\Models\HR\Staff;
use App\Models\Department;
use App\Models\Position;

class FlowDataController extends Controller {

    /**
     * 工作流属性
     * @return type
     */
    public function flowAttribute(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowAttribute');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        $view_user_show = '';
        if(!empty($data['view_user']) && 'null' != $data['view_user'])
        {
            $view_user = explode(",", $data['view_user']);
            $staffinfo = Staff::whereIn('staff_sn',$view_user)->get()->toArray();
            
            if(!empty($staffinfo))
            {
                foreach ($staffinfo as $staffinfoKey => $staffinfoValue) {
                    $view_user_show .= $staffinfoValue['realname'].',';
                }
            }
        }
        $data['view_user_show'] = $view_user_show;
        $view_role_show = '';
        if(!empty($data['view_role']) && 'null' != $data['view_role'])
        {
            $view_role = explode(",", $data['view_role']);
            $roleinfo = Position::whereIn('id',$view_role)->get()->toArray();   
            if(!empty($roleinfo))
            {
                foreach ($roleinfo as $roleinfoKey => $roleinfoValue) {
                    $view_role_show .= $roleinfoValue['name'].',';
                }
            }
        }
        $data['view_role_show'] = $view_role_show;
        $view_dept_show = '';
        if(!empty($data['view_dept']) && 'null' != $data['view_dept'])
        {
            $view_dept = explode(",", $data['view_dept']);
            $deptinfo = Department::whereIn('id',$view_dept)->whereIn('parent_id',$view_dept)->get()->toArray();
            if(!empty($deptinfo))
            {
                foreach ($deptinfo as $deptinfoKey => $deptinfoValue) {
                    $view_dept_show .= $deptinfoValue['name'].',';
                }
            }
        }
        $data['view_dept_show'] = $view_dept_show;
        if (isset($request['skip'])) {
            return view('workflow.flow.flow_attribute', ['data' => $data, 'skip' => $request['skip']]);
        } else {
            return view('workflow.flow.flow_data_page', ['data' => $data]);
        }
    }

    /**
     * 设计流程 预览表单
     * @param Request $request
     */
    public function flowDesignPreview(Request $request) {
        $flow_id = $request->flow_id;
        $url = config('api.url.workflow.flowDesignFormGetFormId'); //通过流程flow_id去查询到表单form_id
        $form_id = Curl::setUrl($url)->sendMessageByPost(['flow_id' => $flow_id]); //表单form_id
        
        $desigin_url = config('api.url.workflow.formDesignUpdate');//设计流程  预览表单
        $result = Curl::setUrl($desigin_url)->sendMessageByPost(['form_id' => $form_id]);
        $data = $result['template'];
        return view('workflow.preview')->with(['data'=>$data]);
       // return Response::view('workflow.preview', ['data' => $data])->header('X-XSS-Protection', 0);
    }

}
