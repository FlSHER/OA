<div class="input-3level-group">
    <select class="form-control" name="{{$provinceName}}" title="省">
        {!!$HRM->getDistrictOptions()!!}
    </select>
    <select class="form-control" name="{{$cityName}}" title="市">

    </select>
    <select class="form-control" name="{{$countyName}}" title="区/县">

    </select>
</div>
<!-- district -->
<script type="text/javascript" src="{{source('js/layout/district.js')}}"></script>