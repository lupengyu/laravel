<!doctype html>

<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <title>青春521绑定邮箱</title>
    <script type="text/javascript">
        var InterValObj; //timer变量，控制时间
        var count = 60; //间隔函数，1秒执行
        var curCount;//当前剩余秒数
        var code = ""; //验证码
        var codeLength = 6;//验证码长度
        function sendMessage() {
            curCount = count;
            var dealType; //验证方式
            var uid=$("#uid").val();//用户uid
            if ($("#phone").attr("checked") == true) {
                dealType = "phone";
            }
            else {
                dealType = "email";
            }
//产生验证码
            for (var i = 0; i < codeLength; i++) {
                code += parseInt(Math.random() * 9).toString();
            }
//设置button效果，开始计时
            $("#btnSendCode").attr("disabled", "true");
            $("#btnSendCode").val( + curCount + "秒再获取");
            InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
//向后台发送处理数据
            $.ajax({
                type: "POST", //用POST方式传输
                dataType: "text", //数据格式:JSON
                url: 'Login.ashx', //目标地址
                data: "dealType=" + dealType +"&uid=" + uid + "&code=" + code,
                error: function (XMLHttpRequest, textStatus, errorThrown) { },
                success: function (msg){ }
            });
        }
        //timer处理函数
        function SetRemainTime() {
            if (curCount == 0) {
                window.clearInterval(InterValObj);//停止计时器
                $("#btnSendCode").removeAttr("disabled");//启用按钮
                $("#btnSendCode").val("重新发送验证码");
                code = ""; //清除验证码。如果不清除，过时间后，输入收到的验证码依然有效
            }
            else {
                curCount--;
                $("#btnSendCode").val( + curCount + "秒再获取");
            }
        }
    </script>
    <link rel="stylesheet" href="/static/index/index/css/style.css">
    <link rel="stylesheet" href="/static/index/index/css/iconfont.css">
    <script src="/static/index/index/js/jquery.js"></script>
    <style type="text/css">
        #getKey{width: 80px;border: none;outline: none;height: 50px;line-height: 50px;font-size: 16px;position: absolute;right: 10px;top: 0px;}
    </style>
</head>
<body>
<div class="login-banner"></div>
<div class="login-box">
    <div class="box-con tran">
        <div class="login-con f-l">
            <form role="form" method="post" enctype="multipart/form-data" action="/setemail">
                <div style="margin-top: 10pt;margin-left: 80pt">
                    <h3>绑定邮箱</h3>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="邮箱" maxlength="50">
                    <span class="error-notic"></span>
                </div>
                <font size="2" color="red">
                    @if($warning != null)
                        {{$warning}}
                    @endif
                </font>
                <div class="from-line"></div>
                <div class="form-group">
                    <input type="submit" id="login" class="button tran" value="确定" style="background: #03a9f4; color: white">
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>