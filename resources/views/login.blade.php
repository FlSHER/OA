<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" href="{{source('images/logo_icon.png')}}" type="image/png">
    <title>喜歌实业</title>
</head>
<body style="background:#f0f2f6 url('/images/login-bg.svg') center no-repeat fixed;background-size: cover;">
<div id="view"></div>
<script>
    window.CorpId = '{{config('dingding.CorpId')}}';
</script>
<script src="{{source('js/login.js')}}"></script>
</body>
</html>
