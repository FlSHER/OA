<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class UpdateDistrictJs extends Command {

    protected $tableName = 'i_district';
    protected $province;
    protected $city;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:district-js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make area.js using location data in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->province = DB::table($this->tableName)->where('level', 1)->get();
        $this->city = DB::table($this->tableName)->where('level', 2)->get();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->makeBladeFile();
        $this->makeJavascriptFile();
    }

    private function makeBladeFile() {
        $content = $this->makeBladeContent();
        $fileName = resource_path('views/layouts/district_group.blade.php');
        $this->makeFile($fileName, $content);
    }

    private function makeJavascriptFile() {
        $bindContent = $this->makeBindContent();
        $objectContent = $this->makeObjectContent();
        $jsContent = $bindContent . $objectContent;
        $fileName = public_path('js/layout/district.js');
        $this->makeFile($fileName, $jsContent);
    }

    private function makeFile($fileName, $content) {
        if (!file_exists($fileName)) {
            fopen($fileName, 'w');
        }
        file_put_contents($fileName, $content);
    }

    private function makeBladeContent() {
        $content = '<div class="input-3level-group">
    <select class="form-control" name="{{$provinceName}}" title="省">
        <option value=0>-- 无 --</option>';
        foreach ($this->province as $province) {
            $content .= '
        <option value="' . $province->id . '">' . $province->name . '</option>';
        }
        $content .= '
    </select>
    <select class="form-control" name="{{$cityName}}" title="市">

    </select>
    <select class="form-control" name="{{$countyName}}" title="区/县">

    </select>
</div>
<!-- district -->
<script type="text/javascript" src="{{source(\'js/layout/district.js\')}}"></script>';
        return $content;
    }

    private function makeBindContent() {
        $bindContent = '
$(function () {
    $(".input-3level-group select:not(:last)").on("change", new District().getOptions);
});
';
        return $bindContent;
    }

    private function makeObjectContent() {
        $functionContent = $this->makeFunctionContent();
        $optionsContent = $this->makeDistrictOptions();
        $objectContent = '
function District() {
    var self = this;
    this.getOptions = ' . $functionContent . ';
    this.options = ' . $optionsContent . ';
}';
        return $objectContent;
    }

    private function makeFunctionContent() {
        // 获取区划选项
        $funGetDistrictOptions = 'function (e) {
        if (e instanceof HTMLSelectElement) {
            var select = e;
        } else {
            var select = this;
        }
        var parentId = $(select).val();
        var nextTag = $(select).next("select");
        if (parentId.length === 0) {
            nextTag.html("<option value>全部</option>");
        } else {
            var msg = "<option value=0>-- 无 --</option>" + self.options[parentId];
            if ($(select).find("option:first").val().length === 0) {
                msg = "<option value>全部</option>" + msg;
            }
            nextTag.html(msg);
            nextTag.find("option").each(function () {
                if ($(select).val() == nextTag.val()) {
                    return false;
                }
            });
        }
        nextTag.change();
    }';

        $functionContent = $funGetDistrictOptions;
        return $functionContent;
    }

    private function makeDistrictOptions() {
        $districtContent = $this->makeSubDistrictContent(array_collapse([$this->province, $this->city]));
        $districtData = $districtContent;
        return $districtData;
    }

    private function makeSubDistrictContent($parent) {
        $subContent = '{';
        foreach ($parent as $v) {
            $parentId = $v->id;
            $options = DB::table($this->tableName)->where('parent_id', $parentId)
//                            ->orderByRaw('convert( `name` USING gbk ) COLLATE gbk_chinese_ci')
                            ->get()->map(function($value) {
                        return '<option value="' . $value->id . '">' . $value->name . '</option>';
                    })->all();
            $subContent .= '
        ' . $parentId . ': \'' . implode('', $options) . '\',';
        }
        $subContent = trim($subContent, ',') . '
    }';
        return $subContent;
    }

}
