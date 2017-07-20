<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class UpdateDistrictJs extends Command {

    protected $tableName = 'i_district';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $bindContent = $this->makeBindContent();
        $objectContent = $this->makeObjectContent();
        $jsContent = $bindContent . $objectContent;
        $fileName = public_path('js/layout/district.js');
        if (!file_exists($fileName)) {
            fopen($fileName, 'w');
        }
        file_put_contents($fileName, $jsContent);
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
        $tableName = $this->tableName;
        $province = DB::table($tableName)->where('level', 1)->get();
        $city = DB::table($tableName)->where('level', 2)->get();
        $districtContent = $this->makeSubDistrictContent(array_collapse([$province, $city]));
        $districtData = $districtContent;
        return $districtData;
    }

    private function makeSubDistrictContent($parent) {
        $subContent = '{';
        foreach ($parent as $v) {
            $parentId = $v->id;
            $options = DB::table($this->tableName)->where('parent_id', $parentId)->get()->map(function($value) {
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
