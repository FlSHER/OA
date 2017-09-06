<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/5
 * Time: 17:09
 */

namespace App\Repositories\Finance\Reimburse;


use App\Contracts\ExcelExport;

class ReimburseExport extends ExcelExport
{

    public function setClassAttr($path, $fileName, $trans)
    {
        $this->filePath = $path;
        $this->fileName = $fileName;
        $this->trans($trans);
    }

    public function exports($request, $path, $fileName, $trans = array())
    {
        $this->setClassAttr($path, $fileName, $trans);
        return $this->export($request);
    }
}