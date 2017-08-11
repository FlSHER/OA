<?php

/**
 * Excel导入基类
 * create by Fisher 2017/7/2 <fisher9389@sina.com>
 */

namespace App\Contracts;

use Excel;

class ExcelExport {

    protected $filePath;
    protected $fileName;
    protected $availableExtension = ['xlsx', 'xls', 'csv'];
    protected $extension = 'xlsx';
    protected $columns = [];
    protected $localization;

    /**
     * 配置导出文件的类型
     * @param type $extension
     * @return \App\Contracts\ExcelExport
     */
    public function extension($extension) {
        if (in_array($extension, $this->availableExtension)) {
            $this->extension = $extension;
        } else {
            abort(500, '非法文件类型');
        }
        return $this;
    }

    /**
     * 配置本地化
     * @param type $transPath
     * @return \App\Contracts\ExcelExport
     */
    public function trans($transPath) {
        if (is_array($transPath) && array_keys($transPath) == range(0, count($transPath) - 1)) {
            foreach ($transPath as $v) {
                $trans = array_dot(trans($v));
                $this->localization = array_collapse([$this->localization, $trans]);
            }
        } elseif (is_array($transPath)) {
            $this->localization = array_dot($transPath);
        } elseif (is_string($transPath)) {
            $this->localization = array_dot(trans($transPath));
        }
        return $this;
    }

    /**
     * 配置文件路径
     * @param type $filePath
     * @return \App\Contracts\ExcelExport
     */
    public function setPath($filePath) {
        $this->filePath = trim($filePath, '/') . '/';
        return $this;
    }

    /**
     * 配置文件名，可以直接拼接路径
     * @param type $baseName
     * @return \App\Contracts\ExcelExport
     */
    public function setBaseName($baseName) {
        $this->fileName = $baseName;
        return $this;
    }

    /**
     * 配置字段过滤
     * @param type $columns
     * @return \App\Contracts\ExcelExport
     */
    public function setColumns(array $columns) {
        $this->columns = $columns;
        return $this;
    }

    /**
     * 执行导出
     * @param type $data
     * @return type
     */
    public function export(array $data) {
        $file = $this->makeFile($data);
        if (request()->isXmlHttpRequest()) {
            $file->save($this->extension);
            return $file->getFileName();
        } else {
            $file->download($this->extension);
        }
    }

    protected function makeFile(array $data) {
        $fileName = $this->makeFileName();
        $file = Excel::create($fileName, function($excel)use($data) {
                    foreach ($data as $sheetName => $sheetData) {
                        $this->makeSheet($excel, $sheetName, $sheetData);
                    }
                });
        return $file;
    }

    protected function makeSheet($excel, $sheetName, $data) {
        $excel->sheet($sheetName, function($sheet)use($data) {
            if (!empty($this->columns)) {
                $data = $this->filter($data);
            }
            if (!empty($this->localization)) {
                $data = $this->localize($data);
            }
            $sheet->with($data);
            $this->makeBasicStyle($sheet);
        });
    }

    protected function filter(array $data) {
        $filteredData = array_map(function($value) {
            $response = [];
            foreach ($this->columns as $k => $v) {
                $column = isArray($v) ? $v['data'] : $v;
                is_numeric($k) && $name = empty($v['name']) ? $column : $v['name'];
                if (isArray($column))
                    $name = empty($column['name']) ? $column['data'] : $column['name'];
                $cell = array_get($value, $column);
                array_set($response, $name, is_array($cell) ? implode(',', $cell) : $cell);
            }
            return $response;
        }, $data);
        return $filteredData;
    }

    protected function localize($sheetData) {
        $response = [];
        foreach ($sheetData as $row) {
            $newRow = [];
            foreach (array_dot($row) as $field => $value) {
                $newField = array_has($this->localization, $field) ? $this->localization[$field] : $field;
                $newRow[$newField] = $value;
            }
            $response[] = $newRow;
        }
        return $response;
    }

    protected function makeBasicStyle($sheet) {
        $sheet->row('1', function($row) {
            $row->setBackground('#26651e');
            $row->setFontColor('#ffffff');
            $row->setFontWeight('bold');
            $row->setAlignment('center');
        });
        $sheet->freezeFirstRow();
    }

    protected function makeFileName() {
        $exportPath = storage_path('exports/' . $this->filePath);
        if (!is_dir($exportPath)) {
            mkdir($exportPath, 0777, true);
        }
        return $this->filePath . $this->fileName . '-' . date('YmdHis') . '-' . app('CurrentUser')->getStaffSn();
    }

}
