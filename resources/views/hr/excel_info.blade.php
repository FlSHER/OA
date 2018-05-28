<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <table>
            @foreach($department as $v)
            {!! $v->excel_td !!}
            @endforeach
        </table>
        <table>
            @foreach($status as $v)
            <tr>
                <td>{{$v->name}}</td><td>{{$v->id}}</td>
            </tr>
            @endforeach
        </table>
        <table>
            @foreach($brand as $v)
            <tr>
                <td>{{$v->name}}</td>
                @foreach($v->position as $position)
                <td>{{$position->name}}</td>
                @endforeach
            </tr>
            @endforeach
        </table>
        <table>
            @foreach($shop as $v)
            <tr>
                <td>{{$v->shop_sn}}</td><td>{{$v->name}}</td>
            </tr>
            @endforeach
        </table>
    </body>
</html>