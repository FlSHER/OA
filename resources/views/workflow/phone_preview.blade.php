<!DOCTYPE html>
<html lang="en">
    <!--phone_preview-->
    <head>
        <meta charset="UTF-8">
        <title>移动模板</title>
        <link rel="stylesheet" href="{{asset('css/workflow/phone_preview_css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/workflow/phone_preview_css/bootstrap.css')}}">
        <link rel="stylesheet" href="{{asset('css/workflow/phone_preview_css/phone.css')}}">
        <link rel="stylesheet" href="{{asset('css/workflow/phone_preview_css/style.css')}}">
    </head>
    <body>
        @if(!empty($data))
        <section class="col-lg-12">
            <div class="panel">
                <div class="panel-body flow_data_table">
                    @foreach($data as $v)
                    {!! $v !!}
                    @endforeach
                </div>
            </div>
        </section>
        @else
        <div style="margin: 0 auto;width:300px;color:red;">
            内容为空<br/><br/>
            <a href="javascript:window.close();">关闭</a>
        </div>
        @endif
    </body>
    <script type="text/javascript" src="{{asset('/js/workflow/Formdesign4_1_Ueditor1_4_3/js/ueditor/formdesign/My97DatePicker/WdatePicker.js')}}"></script>
</html>
