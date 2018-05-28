<?php

namespace App\Services\DingTalk;

/**
 * dingtalk API: dingtalk.smartwork.bpms.processinstance.create request
 *
 * @author auto create
 * @since 1.0, 2017.06.16
 */
class SmartworkBpmsProcessinstanceCreateRequest
{
    /**
     * 企业微应用标识
     **/
    private $agentId;

    /**
     * 审批人userid列表
     **/
    private $approvers;

    /**
     * 抄送人userid列表
     **/
    private $ccList;

    /**
     * 抄送时间,分为（START,FINISH,START_FINISH）
     **/
    private $ccPosition;

    /**
     * 发起人所在的部门
     **/
    private $deptId;

    /**
     * 审批流表单参数
     **/
    private $formComponentValues;

    /**
     * 审批实例发起人的userid
     **/
    private $originatorUserId;

    /**
     * 审批流的唯一码
     **/
    private $processCode;

    private $apiParas = array();

    public function setAgentId($agentId)
    {
        $this->agentId              = $agentId;
        $this->apiParas["agent_id"] = $agentId;
    }

    public function getAgentId()
    {
        return $this->agentId;
    }

    public function setApprovers($approvers)
    {
        $this->approvers             = $approvers;
        $this->apiParas["approvers"] = $approvers;
    }

    public function getApprovers()
    {
        return $this->approvers;
    }

    public function setCcList($ccList)
    {
        $this->ccList              = $ccList;
        $this->apiParas["cc_list"] = $ccList;
    }

    public function getCcList()
    {
        return $this->ccList;
    }

    public function setCcPosition($ccPosition)
    {
        $this->ccPosition              = $ccPosition;
        $this->apiParas["cc_position"] = $ccPosition;
    }

    public function getCcPosition()
    {
        return $this->ccPosition;
    }

    public function setDeptId($deptId)
    {
        $this->deptId              = $deptId;
        $this->apiParas["dept_id"] = $deptId;
    }

    public function getDeptId()
    {
        return $this->deptId;
    }

    public function setFormComponentValues($formComponentValues)
    {
        $this->formComponentValues               = $formComponentValues;
        $this->apiParas["form_component_values"] = $formComponentValues;
    }

    public function getFormComponentValues()
    {
        return $this->formComponentValues;
    }

    public function setOriginatorUserId($originatorUserId)
    {
        $this->originatorUserId               = $originatorUserId;
        $this->apiParas["originator_user_id"] = $originatorUserId;
    }

    public function getOriginatorUserId()
    {
        return $this->originatorUserId;
    }

    public function setProcessCode($processCode)
    {
        $this->processCode              = $processCode;
        $this->apiParas["process_code"] = $processCode;
    }

    public function getProcessCode()
    {
        return $this->processCode;
    }

    public function getApiMethodName()
    {
        return "dingtalk.smartwork.bpms.processinstance.create";
    }

    public function getApiParas()
    {
        return $this->apiParas;
    }

    public function check()
    {

        RequestCheckUtil::checkNotNull($this->approvers, "approvers");
        RequestCheckUtil::checkMaxListSize($this->approvers, 20, "approvers");
        RequestCheckUtil::checkMaxListSize($this->ccList, 20, "ccList");
        RequestCheckUtil::checkNotNull($this->deptId, "deptId");
        RequestCheckUtil::checkNotNull($this->originatorUserId, "originatorUserId");
        RequestCheckUtil::checkNotNull($this->processCode, "processCode");
    }

    public function putOtherTextParam($key, $value)
    {
        $this->apiParas[$key] = $value;
        $this->$key           = $value;
    }
}



/**
 * API入参静态检查类
 * 可以对API的参数类型、长度、最大值等进行校验
 *
 **/
class RequestCheckUtil
{
    /**
     * 校验字段 fieldName 的值$value非空
     *
     **/
    public static function checkNotNull($value, $fieldName)
    {

        if (self::checkEmpty($value)) {
            throw new \Exception("client-check-error:Missing Required Arguments: " . $fieldName, 40);
        }
    }

    /**
     * 检验字段fieldName的值value 的长度
     *
     **/
    public static function checkMaxLength($value, $maxLength, $fieldName)
    {
        if (!self::checkEmpty($value) && mb_strlen($value, "UTF-8") > $maxLength) {
            throw new \Exception("client-check-error:Invalid Arguments:the length of " . $fieldName . " can not be larger than " . $maxLength . ".", 41);
        }
    }

    /**
     * 检验字段fieldName的值value的最大列表长度
     *
     **/
    public static function checkMaxListSize($value, $maxSize, $fieldName)
    {

        if (self::checkEmpty($value)) {
            return;
        }

        $list = preg_split("/,/", $value);
        if (count($list) > $maxSize) {
            throw new \Exception("client-check-error:Invalid Arguments:the listsize(the string split by \",\") of " . $fieldName . " must be less than " . $maxSize . " .", 41);
        }
    }

    /**
     * 检验字段fieldName的值value 的最大值
     *
     **/
    public static function checkMaxValue($value, $maxValue, $fieldName)
    {

        if (self::checkEmpty($value)) {
            return;
        }

        self::checkNumeric($value, $fieldName);

        if ($value > $maxValue) {
            throw new \Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " can not be larger than " . $maxValue . " .", 41);
        }
    }

    /**
     * 检验字段fieldName的值value 的最小值
     *
     **/
    public static function checkMinValue($value, $minValue, $fieldName)
    {

        if (self::checkEmpty($value)) {
            return;
        }

        self::checkNumeric($value, $fieldName);

        if ($value < $minValue) {
            throw new \Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " can not be less than " . $minValue . " .", 41);
        }
    }

    /**
     * 检验字段fieldName的值value是否是number
     *
     **/
    protected static function checkNumeric($value, $fieldName)
    {
        if (!is_numeric($value)) {
            throw new \Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " is not number : " . $value . " .", 41);
        }

    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     *
     *
     **/
    public static function checkEmpty($value)
    {
        if (!isset($value)) {
            return true;
        }

        if ($value === null) {
            return true;
        }

        if (is_array($value) && count($value) == 0) {
            return true;
        }

        if (is_string($value) && trim($value) === "") {
            return true;
        }

        return false;
    }

}
