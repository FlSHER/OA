<div class="input-3level-group">
    <select class="form-control" name="{{$provinceName}}" title="省">
        <option value=0>-- 无 --</option>
        <option value="110000">北京市</option>
        <option value="120000">天津市</option>
        <option value="130000">河北省</option>
        <option value="140000">山西省</option>
        <option value="150000">内蒙古自治区</option>
        <option value="210000">辽宁省</option>
        <option value="220000">吉林省</option>
        <option value="230000">黑龙江省</option>
        <option value="310000">上海市</option>
        <option value="320000">江苏省</option>
        <option value="330000">浙江省</option>
        <option value="340000">安徽省</option>
        <option value="350000">福建省</option>
        <option value="360000">江西省</option>
        <option value="370000">山东省</option>
        <option value="410000">河南省</option>
        <option value="420000">湖北省</option>
        <option value="430000">湖南省</option>
        <option value="440000">广东省</option>
        <option value="450000">广西壮族自治区</option>
        <option value="460000">海南省</option>
        <option value="500000">重庆市</option>
        <option value="510000">四川省</option>
        <option value="520000">贵州省</option>
        <option value="530000">云南省</option>
        <option value="540000">西藏自治区</option>
        <option value="610000">陕西省</option>
        <option value="620000">甘肃省</option>
        <option value="630000">青海省</option>
        <option value="640000">宁夏回族自治区</option>
        <option value="650000">新疆维吾尔自治区</option>
        <option value="710000">台湾省</option>
        <option value="810000">香港特别行政区</option>
        <option value="820000">澳门特别行政区</option>
    </select>
    <select class="form-control" name="{{$cityName}}" title="市">

    </select>
    <select class="form-control" name="{{$countyName}}" title="区/县">

    </select>
</div>
<!-- district -->
<script type="text/javascript" src="{{source('js/layout/district.js')}}"></script>