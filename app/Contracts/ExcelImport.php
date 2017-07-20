<?php

/**
 * Excel导入基类
 * create by Fisher 2017/6/16 <fisher9389@sina.com>
 */

namespace App\Contracts;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Excel;

class ExcelImport {

    protected $filePath;
    protected $availableExtension = ['xlsx', 'xls', 'csv', 'bin'];
    protected $localization = [];

    public function load(Request $request = null, $fileName = null) {
        if (empty($request))
            $request = request();
        $fileList = $this->getFile($request, $fileName);
        if (count($fileList) > 1) {
            $data = [];
            foreach ($fileList as $name => $file) {
                $data[$name] = $this->readFile($file);
            }
            return $data;
        } elseif (count($fileList) == 1) {
            return $this->readFile(array_first($fileList));
        } else {
            throw new \Illuminate\Http\Exception\HttpResponseException(response('无法读取有效文件', 500));
        }
    }

    public function getFile(Request $request, $fileName = null) {
        $fileList = $request->file($fileName);
        $availableFiles = [];
        foreach ((array) $fileList as $name => $file) {
            if ($this->isAvailable($file)) {
                $availableFiles[$name] = $file;
            }
        }
        return $availableFiles;
    }

    public function extension($extension) {
        if (is_string($extension)) {
            $extension = [$extension];
        }
        $extension[] = 'bin';
        $this->availableExtension = $extension;
        return $this;
    }

    public function trans($transPath) {
        if (is_array($transPath)) {
            foreach ($transPath as $v) {
                $trans = array_flip(array_dot(trans($v)));
                $this->localization = array_collapse([$this->localization, $trans]);
            }
        } elseif (is_string($transPath)) {
            $this->localization = array_flip(array_dot(trans($transPath)));
        }
        return $this;
    }

    protected function readFile($file) {
        $heading = Excel::selectSheetsByIndex(0)->noHeading()->load($file)->first()->all();
        $columns = $this->makeColumns($heading);
        $data = Excel::skip(1)->take(false)->get()->map(function($row)use($columns) {
                    if (!empty(array_filter($row->all(0)))) {
                        return $this->getRowData($row, $columns);
                    } else {
                        return false;
                    }
                })->filter()->all();
        return $data;
    }

    protected function isAvailable(UploadedFile $file) {
        $extension = $file->guessClientExtension();
        if ($file->isValid() && in_array($extension, $this->availableExtension)) {
            return true;
        } else {
            return false;
        }
    }

    protected function makeColumns($heading) {
        $columns = [];
        foreach ($heading as $key => $column) {
            if (empty($column)) {
                continue;
            } elseif (preg_match('/^[\x80-\xff ]$/', $column) != -1 && !empty($this->localization)) {//全中文时匹配翻译
                $columns[$key] = array_has($this->localization, $column) ? $this->localization[$column] : $column;
            } else {
                $columns[$key] = preg_replace(['/([\x80-\xff \]]*)/i', '/\[/'], ['', '.'], $column); //去除中文和空格，将数组改为点分割字符串
            }
        }
        return $columns;
    }

    protected function getRowData($row, $columns) {
        $rowData = [];
        foreach ($row as $index => $column) {
            if (array_has($columns, $index)) {
                $rowData = array_add($rowData, $columns[$index], is_null($column) ? '' : $column);
            }
        }
        return $rowData;
    }

}
