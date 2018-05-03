$(function() {

    var login = function() {
        var info = $("#loginForm").serialize();
        $.ajax({
            type: "post",
            url: '/teacher/Login/login',
            traditional: true,
            dataType: "json",
            data: info,
            success: function(data) {
            	if (data['valid']) {
            		location.href='/teacher/Index/index';
            	} else{
	               layer.msg(data['msg'],{
                        icon: 2, //提示的样式
                        time: 700, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    });
                    captcha()

            	}
            }
        });
    }
    var captcha = function (){
        var src = $('#captcha').attr('src')
        $('#captcha').attr('src',src+'?'+Math.random());
    }

    var editPsw = function() {
        var info = $("#editPswForm").serialize();
        $.ajax({
            type: "post",
            url: '/index/Login/editPswFn',
            traditional: true,
            dataType: "json",
            data: info,
            success: function(data) {
                if (data['valid']) {
                    layer.open({
                        type: 0,
                        content: data.msg,
                        btn: '我知道了',
                        yes: function(index) {
                            window.location.href = "/index/Login/index"
                        }
                    })
                } else {
                    layer.open({
                        type: 0,
                        content: data.msg,
                        btn: '我知道了',
                    })
                }
            }
        });
    }

    var _main = function() {
        $('#login').click(function() {
            login()
        })
        $('#editPsw').click(function() {
            editPsw()
        })

        $('body').keypress(function(event) {
            var keynum = (event.keyCode ? event.keyCode : event.which);
            if (keynum == '13') {
                if ($('#loginForm').length > 0) {
                    login()
                } else {
                    editPsw()
                }
            }
        });
        $('#captcha').click(function(){
            captcha()
        })
    }
    _main()
})