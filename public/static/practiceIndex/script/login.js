$(function() {

    var login = function() {
        var info = $("#loginForm").serialize();

        $.ajax({
            type: "post",
            url: '/index/Login/login',
            traditional: true,
            dataType: "json",
            data: info,
            success: function(data) {
                if (data['valid']) {
                    window.location.href = "/index/Index/index"
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
    }
    _main()
})