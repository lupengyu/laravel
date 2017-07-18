<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>青春521</title>

    <link rel="stylesheet" type="text/css" href="\static\login\css/default.css">

    <!--必要样式-->
    <link rel="stylesheet" type="text/css" href="\static\login\css/styles.css">
    <!--[if IE]>
    <script src="http://libs.baidu.com/html5shiv/3.7/html5shiv.min.js"></script>
    <![endif]-->
    <style type="text/css">
        div#backimg{
            position:fixed;
            top:0;
            left:0;
            bottom:0;
            right:0;
            z-index:-1;
        }
        div#backimg > img {
            height:100%;
            width:100%;
            border:0;
        }
    </style>

</head>
<body>
<div id="backimg"><img src="\static\login\img\login_bakimg(new).jpg" /></div>
<div class='login'>
    <div class='login_title'>
        <span>账号登录</span>
    </div>
    <div class='login_fields'>
        <FORM method="post" class="form" action="login">
            <div class='login_fields__user'>
                <div class='icon'>
                    <img src='\static\login\img\user_icon_copy.png'>
                </div>
                <input placeholder='用户名(默认为学号,外校为邮箱)' type='text' name="username" maxlength="50">
                <div class='validation'>
                    <img src='\static\login\img/tick.png'>
                </div>
                </input>
            </div>
            <div class='login_fields__password'>
                <div class='icon'>
                    <img src='\static\login\img\lock_icon_copy.png'>
                </div>
                <input placeholder='密码(默认为生日 例19900101)' type='password' name="password" maxlength="18">
                <div class='validation'>
                    <img src='\static\login\img/tick.png'>
                </div>
            </div>
            <div class='login_fields__password'>
                <div class='icon'>

                </div>
                <input placeholder='验证码' type='text' name="code">
                <div class='validation'>
                    <img src='\static\login\img\tick.png'>
                </div>
            </div>
            <div style="width: 100%;height: 60px;">
                <!--
                <img src="{:captcha_src()}" alt="captcha" onClick="this.src=this.src+'?'+Math.random()" align="right"/>
                -->
                <img src="{{ URL('captcha/1') }}"  alt="验证码" title="刷新图片" width="180" height="80" onClick="this.src=this.src+'?'+Math.random()" id="c2c98f0de5a04167a9e427d883690ff6" border="0"  align="right">
            </div>
            @if($warning != null)
                <div style=" width: 100%;max-height: 20px"><font color="red">{{$warning}}</font></div>
                @else
                <div style="height: 20px;"></div>
            @endif
            <div class='login_fields__submit login_fields__submit2'>
                <input type='submit' value='登录'> <button onclick="register()" value='注册' type="button">注册</button>
                <div class='forgot'>
                    <a href='/tourists'>游客身份登录</a>
                    <a style="padding-left: 10px;" href='/losspassword'>忘记密码?</a>
                </div>
            </div>
        </FORM>
        <!--
        <div class='login_fields__submit2' style="padding-top: 20px;">
            <button onclick="register()" value='注册'>注册</button>
            </br>
        </div>

        <div class='login_fields__submit3' style="padding-top: 20px;">
            <button onclick="tourists();" value='游客'>游客</button>
                </br>
                <div class='forgot'>
                    <a href='{:url('index/index/losspassword')}'>忘记密码?</a>
                </div>
        </div>
        -->

    </div>

</div>
</div>
<script type="text/javascript">
    function register() {
        window.location.href = "http://localhost/register";
    }
</script>
<script type="text/javascript" src='\static\login\js/stopExecutionOnTimeout.js?t=1'></script>
<script src="http://www.jq22.com/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="\static\login\js/jquery-ui.min.js"></script>
</body>
</html>