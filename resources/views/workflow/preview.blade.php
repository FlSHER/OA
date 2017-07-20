<!DOCTYPE html>
<html lang="en">
    <!--preview-->
    <head>
        <meta charset="UTF-8">
        <meta name="_token" content="{{csrf_token()}}">
        <title>表单预览</title>
    </head>
    <body>
        @if(!empty($data))
        <?php echo $data; ?>
        @else
        <div style="margin: 0 auto;width:300px;color:red;">
            内容为空<br/><br/>
            <a href="javascript:window.close();">关闭</a>
        </div>
        @endif

    </body>
    <script type="text/javascript" src="{{source('/js/workflow/Formdesign4_1_Ueditor1_4_3/js/ueditor/formdesign/My97DatePicker/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{source('/js/jquery-3.1.1.min.js')}}"></script>
    <!--宏控件-->
    <script type="text/javascript" src="{{source('js/workflow/form/macros.js')}}"></script>
    <!--列表控件-->
    <script type="text/javascript" src="{{source('js/workflow/form/listCtrl.js')}}"></script>
</html>