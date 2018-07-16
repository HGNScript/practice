$(function() {

    var fontSize = function() {
        var docEl = document.documentElement
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize';
        recalc = function() {
            var clientWidth = docEl.clientWidth
            if (!clientWidth) return
            if (clientWidth >= 640) {
                docEl.style.fontSize = '100px'
            } else {
                docEl.style.fontSize = 100 * (clientWidth / 640) + 'px';
            }
        };

        if (!document.addEventListener) return;
        window.addEventListener(resizeEvt, recalc, false);
        document.addEventListener('DOMContentLoaded', recalc, false);
        recalc();
    }

    var swiper = new Swiper('.swiper-container', {
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    var downAnUp = function() {
        var clickFlag = false
        $('#down').click(function() {
            if (!clickFlag) {
                $(".info").show("200")
                $(this).addClass("fa-angle-double-up")
                $(this).removeClass("fa-angle-double-down")
                clickFlag = true
            } else {
                $(".info").not($(".info")[0]).not($(".info")[1]).hide("200")
                $(this).addClass("fa-angle-double-down")
                $(this).removeClass("fa-angle-double-up")
                clickFlag = !clickFlag
            }

        })
    }

    var gd = function() {
        var mapObj = new AMap.Map('iCenter');

        mapObj.plugin('AMap.Geolocation', function() {
            geolocation = new AMap.Geolocation({
                enableHighAccuracy: true, //是否使用高精度定位，默认:true
                timeout: 10000, //超过10秒后停止定位，默认：无穷大
                maximumAge: 0, //定位结果缓存0毫秒，默认：0
                convert: true, //自动偏移坐标，偏移后的坐标为高德坐标，默认：true
                showButton: true, //显示定位按钮，默认：true
                buttonPosition: 'LB', //定位按钮停靠位置，默认：'LB'，左下角
                buttonOffset: new AMap.Pixel(10, 20), //定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                showMarker: true, //定位成功后在定位到的位置显示点标记，默认：true
                showCircle: true, //定位成功后用圆圈表示定位精度范围，默认：true
                panToLocation: true, //定位成功后将定位到的位置作为地图中心点，默认：true
                zoomToAccuracy: true //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
            });
            mapObj.addControl(geolocation)
            geolocation.getCurrentPosition()

            AMap.event.addListener(geolocation, 'complete', onComplete); //  注册对象事件 返回定位信息
            AMap.event.addListener(geolocation, 'error', onError); // 返回定位出错信息
        })

    }

    var onComplete = function(obj) {
        var address = obj.formattedAddress
        $.ajax({
            type: "post",
            url: '/index/Index/signIn',
            data: { address: address},
            beforeSend:function(XMLHttpRequest){
                var index = layer.load();
            },
            success: function(data) {
                layer.open({
                    type: 0,
                    content: data.msg,
                    btn: '我知道了',
                    yes: function(index) {
                        layer.close(index);
                        location.reload();
                    }
                })
            }
        });
    }
    var onError = function(obj) {
        console.log(obj.info + '--' + obj.message)
    }

    var infoAdd = function() {
        var info = $("#addForm").serialize();
        $.ajax({
            type: "post",
            url: '/index/Index/addInfo',
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
                            window.location.href = "/stu"
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
    var infoEdit = function() {
        var info = $("#addForm").serialize();
        $.ajax({
            type: "post",
            url: '/index/Index/editInfo',
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
                            window.location.href = "/stu"
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

    var addLogs = function() {
        var logs_id = $('#logs_id').attr('data-id')

        var html = $('#kindEditor').html();
        $('#text').val(html);
        var info = $("#LogsForm").serialize();

        var str = $('#text').val()
        var strNull = str.indexOf("点击此处编辑内容")


        var str = str.replace(/<[^>]+>/g, ""); //去掉所有的html标记

        function getByteLen(str) {

            var len = 0;

            for (var i = 0; i < str.length; i++) {

                var a = str.charAt(i);

                if (a.match(/[^\x00-\xff]/ig) != null) {

                    len += 2;

                } else {

                    len += 1;

                }

            }

            return len;

        }
        var str = getByteLen(str)
        // if (strNull <= 200) {
        //      layer.open({
        //         type: 0,
        //         content: '字数不得少于200字!',
        //         btn: '我知道了',
        //     })
        //     return false;
        // }


        if (strNull > 0) {
            layer.open({
                type: 0,
                content: '请填写内容!',
                btn: '我知道了',
            })
            return false;
        }
        $.ajax({
            type: "post",
            url: '/index/Index/addlogs?logs_id=' + logs_id,
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
                            window.location.href = "/stu"
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

    var out = function() {
        $.ajax({
            type: "post",
            url: '/index/Index/out',
            traditional: true,
            dataType: "json",
            success: function(data) {

                location.reload();

                // layer.open({
                //     type: 0,
                //     content: data.msg,
                //     btn: '我知道了',
                //     yes: function(index) {
                //         layer.close(index);
                //     }
                // })
            }
        });
    }

    var editPas = function() {
        var info = $("#editPasForm").serialize();
        $.ajax({
            type: "post",
            url: '/editpas',
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
                            window.location.href = "/stu"
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
        fontSize()
        downAnUp()

        $('#editPas').click(function() {
            editPas()
        })
        $('#signIn').click(function() {
            $.ajax({
                type: "post",
                url: '/index/Index/sign',
                success: function(data) {
                    if (data.valid) {
                        layer.open({
                            type: 0,
                            content: data.msg,
                            btn: '我知道了',
                            // yes: function(index) {
                            //     location.reload();
                            // }
                        })
                    } else {
                        // layer.open({
                        //     type: 2,
                        //     content: data.msg,
                        // });
                        gd()

                        // onComplete();
                    }
                }
            });


        })
        $('#infoEdit').click(function() {
            infoEdit()
        })
        $('#infoAdd').click(function() {
            infoAdd()
        })
        $('#addLogs').click(function() {
            layer.open({
                content: '你确定添加日志吗,添加后将不可修改',
                btn: ['添加', '不要'],
                yes: function(index) {
                    addLogs()
                }
            });
        })
        $('#out').click(function() {
            out()
        })
        $('#login').click(function() {
            login()
        })
    }
    _main()
})