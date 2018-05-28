<?php
namespace App\Services;

class WorkFlowService {
	/**
     * 解析模板
     * @$request:html字符串
     */
    public static function paserTemplate($request) {
        $data = $request;
        $preg = "/(\<span(((?!<span).)*leipiplugins=\"(radios|checkboxs|select)\".*?)>(.*?)<\/span>|<(img|input|textarea|select).*?(<\/select>|<\/textarea>|\/>))/s";
        $result = [];
        if (!empty($data) && isset($data)) {
        	preg_match_all($preg, $data, $arr);
        	return $arr;
        	$tag_preg = '/(?<=<)[\x00-\xff]+?(?=\s)/'; //匹配标签
            $type_preg = '/(?<=type=\")[\x00-\xff]+?(?=\")/'; //匹配类型
            $name_preg = '/(?<=name=\")[\x00-\xff]+?(?=\")/'; //匹配name属性
            $title_preg = '/(?<=title=\")[\x00-\xff]+?(?=\")/'; //匹配title属性
            $value_preg = '/(?<=value=\")[\x00-\xff]+?(?=\")/'; //匹配value属性
            $checked_preg = '/(?<=checked=\")[\x00-\xff]+?(?=\")/'; //匹配checked属性
            $radio_preg = '/(?<=radio=\")[\x00-\xff]+?(?=\")/'; //匹配checked属性
            $hidden_preg = '/(?<=hidden=\")[\x00-\xff]+?(?=\")/'; //匹配hidden属性
            $display_preg = '/(?<=display\:)[\x00-\xff]+?(?=\;|\")/'; //匹配display属性
            foreach ($arr[0] as $k => $v) {
            	preg_match($type_preg, $v, $type);
            	preg_match($tag_preg, $v, $tag);
            	preg_match($name_preg, $v, $name);
            	preg_match($title_preg, $v, $title);
            	$result[$k]['type'] = !empty($type)?$type[0]:'';
            	$result[$k]['tag'] = !empty($tag)?$tag[0]:'';
            	$result[$k]['name'] = !empty($name)?$name[0]:'';
            	$result[$k]['title'] = !empty($title)?$title[0]:'';
            }
        }
        return $result;
    }
    /**
     * 截取[数字]中的数字并返回数组
     * $str:prsc_in_set/prsc_out_set字符串
     */
    public static function catOutDigit($str)
    {
        $re = "/(?!\[)\d+?(?=\])/s";
        preg_match_all($re, $str, $numArr);
        return $numArr[0];
    }
    /**
     * 检查条件是否在tbody条件列表中
     * $arguments：是一个参数数组
     * 数组元素信息如下：
     * str:prcs_in/prcs_out条件列表中的值
     * arr:[数字]中的数字组合的数组 由catOutDigit方法获得
     */
    public static function existTbody($arguments)
    {
        $tdStr = '';
        $checkSign = 'true';
        $strArr = explode(',',$arguments['str']);
        foreach ($strArr as $strArrKey => $strArrValue) {
            if(!empty($strArrValue)){
                $tdStr .= $strArrKey +1;
            }
        }
        $tdStr = trim($tdStr);
        foreach ($arguments['arr'] as $arrKey => $arrValue) {
            if(strpos($tdStr, $arrValue) === false){
                $checkSign = 'false';
                break;
            }
        }
        return $checkSign;
    }
    /**
     * 整理字符串
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function neaten($str)
    {
        //整理字符串
        $str = str_replace("（","(",$str);
        $str = str_replace("）",")",$str);
        //将字符串转小写
        $str = strtolower($str);
        //整理字符串去除空格
        $str = str_replace(" ","",$str);
        return $str;
    }
    /**
     * 重新组合字符串
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function newStr($str){
        //整理字符串
        $nStr = self::neaten($str);
        /*匹配'[数字]'和逻辑符括号*/
        $re = '/(\[\d+\])|(and)|(or)|(AND)|(OR)|\(|\)|\w+|[\x4E00-\x9FA5\xF900-\xFA2D]/s';
        /*组合新的标准字符串*/
        $strTmp = '';
        preg_match_all($re, $nStr, $strArr);
        $strArr = $strArr[0];
        foreach ($strArr as $strArrKey => $strArrValue) {
            if(preg_match('/(\[\d+\])/',$strArrValue)){
                $strTmp .= $strArrValue . ' ';
            }
            else if(preg_match('/\(/',$strArrValue)){
                $strTmp .= $strArrValue;
            }
            else if(preg_match('/\)/',$strArrValue)){
                $strTmp = preg_replace('/(^\s*)|(\s*$)/',"",$strTmp);
                $strTmp .= $strArrValue .' ';
            }
            else{
                $strTmp += strtoupper($strArrValue)+' ';
            }
        }
        return  trim($strTmp);
    }
    /**
     * 校验条件设置，转入、转出条件是否正确,如果正确就返回新的字符串
     * @param  [type] $str [前台传回的prsc_in_set/prsc_out_set字符串]
     * @return [type]      [description]
     */
    public static function rollInOut($str)
    {
        $checkSign = 'true';
        //整理字符串
        $str = self::neaten($str);
        /*递归检查*/
        $checkSign = self::recursionBrackets($str);
        return $checkSign;
    }
    /**
     * 检查字符串中是否有非法字符和括号是否成对出现
     */
    public static function checkStrAndBrackets($str)
    {
        $checkSign = 'true';
        /*反向查询,如果有非下面的字符出现则 return false 表示字符串不正确*/
        $re = '/\[\d+\]|and|or|AND|OR|\(|\)/s';
        $s = $str;
        $s = preg_replace($re,"",$s);
        if("" != $s){
            $checkSign = 'false';
            return $checkSign;
        }
        /*检查左括号数量*/
        preg_match_all('/\(/',$str,$lPair);
        $lPair = $lPair[0];
        /*检查右括号数量*/
        preg_match_all('/\)/',$str,$rPair);
        $rPair = $rPair[0];
        if(count($lPair) != count($rPair)){
            $checkSign = 'false';
            return $checkSign;
        }
        /*无括号*/
        if(0 == count($lPair) && 0 == count($rPair)){
            $checkSign = self::checkStr($str,0);
            return $checkSign;
        }
        return $checkSign;
    }
    /**
     * 递归检查
     * @param  [type] $str [neaten方法整理过的字符串]
     * @return [type]      [true/false]
     */
    public static function recursionBrackets($str)
    {
        $checkSign = 'true';
        $checkSign = self::checkStrAndBrackets($str);
        if('false' == $checkSign){
            return $checkSign;
        }
        $re = '/(\[\d+\])|(and)|(or)|(AND)|(OR)|\(|\)/s';
        preg_match_all($re,$str,$strArr);
        $strArr = $strArr[0];
        $rPairIterator = 0;
        $strTmp = '';
        $arr = [];
        foreach ($strArr as $strArrKey => $strArrValue) {
            if(preg_match('/\)/',$strArrValue)){
                $rPairIterator++;
            }
            /*检查第一次出现右括号的位置*/
            if(1 == $rPairIterator){
                $rPairIterator = 0;
                $rArr = [];
                /*截取第一对括号里面的类容*/
                for($ai = count($arr)-1; $ai >= 0; $ai--){
                    if(preg_match('/\(/',$arr[$ai])){
                        break;
                    }
                    $rArr[] = $arr[$ai];
                }
                for($rai = count($rArr)-1; $rai >= 0; $rai--){
                    $strTmp .= $rArr[$rai];
                }
                $strTmp = trim($strTmp);
                /*检查括号里的类容是否合法*/
                $checkSign = self::checkStr($strTmp,0);
                if('true' == $checkSign){
                    $str = str_replace('('.$strTmp.')','[0]',$str);
                    $checkSign = self::recursionBrackets($str);
                    return $checkSign;
                }
                else{
                    return $checkSign;
                }
                $strTmp = '';
            }
            $arr[] = $strArrValue;
        }
        return $checkSign;
    }
    /**
     * 校验字符串是否正确
     * @param  [type] $str     [前台传回的prsc_in_set/prsc_out_set字符串]
     * @param  [type] $bracket [括号标记：1表示有括号，0表示无括号]
     * @return [type]          [description]
     */
    public static function checkStr($str,$bracket)
    {
        $passSign = 'true';
        /*匹配'[数字]'、逻辑符、括号还有其他字符*/
        $re = '/(\[\d+\])|(and)|(or)|(AND)|(OR)|\(|\)|\[|\]|\w+|[\x4E00-\x9FA5\xF900-\xFA2D]/s';
        /*组合新的标准字符串*/
        $prevSign = -1;//记录前一次字符标记(0:表示‘[and|or]’,1:表示'[数字]',2:表示‘(’,3:表示')')
        preg_match_all($re,$str,$strArr);
        $strArr = $strArr[0];
        if(1 == $bracket){
            if(3 >= count($strArr)){
                /*不符合要求的字符串*/
                $passSign = 'false';
                return $passSign;
            }
        }
        else{
            if(3 > count($strArr)){
                /*不符合要求的字符串*/
                $passSign = 'false';
                return $passSign;
            }
        }
        /*检查最后一个元素*/
        $pop = $strArr;
        $pop = array_pop($pop);
        if(!preg_match('/\[\d+\]|\)/',$pop)){
            /*不符合要求的字符串*/
            $passSign = 'false';
            return $passSign;
        }
        foreach ($strArr as $strArrKey => $strArrValue) {
            if(preg_match('/(\[\d+\])/',$strArrValue)){
                $curSign = 1;
            }
            else if(preg_match('/\(/',$strArrValue)){
                $curSign = 2;
            }
            else if(preg_match('/\)/',$strArrValue)){
                $curSign = 3;
            }
            else{
                $curSign = 0;
            }
            if(0 == $strArrKey)//第一个字符
            {
                //如果字符串一开始就是[‘and|or|)’]，就肯定不正确
                if(0 == $curSign || 3 == $curSign){
                    $passSign = 'false';
                    return $passSign;
                }
                $prevSign = $curSign;
            }
            else
            { 
                switch($curSign){
                    case 1:
                        //当前(1:表示‘[数字]’),如果上一字符串不是[0:表示‘[and|or]’,2:表示‘(’],则字符串不符合标准
                        if(0 != $prevSign && 2 != $prevSign){
                            $passSign = 'false';
                            return $passSign;
                        }
                        $prevSign = $curSign;
                        break;
                    case 2:
                        //当前[2:表示‘(’],如果上一字符串不是(0:表示‘[and|or]’),则字符串不符合标准
                        if(0 != $prevSign){
                            $passSign = 'false';
                            return $passSign;
                        }
                        $prevSign = $curSign;
                        break;
                    case 3:
                        //当前[3:表示')'],如果上一字符串不是(1:表示‘[数字]’),则字符串不符合标准
                        if(1 != $prevSign){
                            $passSign = 'false';
                            return $passSign;
                        }
                        $prevSign = $curSign;
                        break;
                    default:
                        //当前(0:表示‘[and|or]’),如果上一字符串不是[1:表示‘[数字]’,3:表示')'],则字符串不符合标准
                        if(1 != $prevSign && 3 != $prevSign){
                            $passSign = 'false';
                            return $passSign;
                        }
                        $prevSign = $curSign;
                }
            }
        }
        return $passSign;
    }
    
}