dd.ready(function () {
    //获取微应用免登授权码
    getRequestAuthCode();
});
dd.error(function (msg) {
    alert(JSON.stringify(msg));
});

//获取微应用免登授权码requestAuthCode
function getRequestAuthCode() {
    dd.runtime.permission.requestAuthCode({
        corpId: CorpId,
        onSuccess: function (result) {
            var params = {
                code: result.code,
                url: $("input[name=url]").val()
            };
            if (result.code) {
                oaWaiting.show();
                $.ajax({
                    type: 'POST',
                    url: '/login-dingtalk',
                    data: params,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                    success: function (msg) {
                        oaWaiting.hide();
                        var status = msg['status'];
                        if (status === 1) {
                            window.location.href = msg['url'];
                        } else if (status === -1) {
                            alert(msg['message']);
                        } else if (status === -2) {
                            var bindInput = $('<input>').attr({
                                'name': 'dingding',
                                'type': 'hidden'
                            }).val(msg['dingding']);
                            $('.form-signin').append(bindInput);
                        }
                    },
                    error: function (err) {
                        oaWaiting.hide();
                        alert(JSON.stringify(err));
                    }
                });

            }
        },
        onFail: function (err) {
            alert(JSON.stringify(err));
        }
    });
}


