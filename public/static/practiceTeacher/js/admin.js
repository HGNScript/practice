$(function() {
    excel();
    page();
    $("#search").click(function() {
        var str = $('#input').val()
        if (!str) {
            layer.msg('请填写您要查询的数据', {
                icon: 2, //提示的样式
                time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                end: function() {
                    layer.closeAll('dialog')
                }
            });
            return 0
        }
        search()
    })
    $('#input').keypress(function(event) {
        var keynum = (event.keyCode ? event.keyCode : event.which);
        if (keynum == '13') {
            var str = $('#input').val()
            if (!str) {
                layer.msg('请填写您要查询的数据', {
                    icon: 2, //提示的样式
                    time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        layer.closeAll('dialog')
                    }
                });
                return 0
            }
            search();
        }
    });
    $('#renovate').mouseover(function() {
        $('#renovate i').addClass('layui-anim layui-anim-rotate layui-anim-loop')
    })
    $('#renovate').mouseout(function() {
        $('#renovate i').removeClass('layui-anim layui-anim-rotate layui-anim-loop')
    })
    $('#renovate').click(function() {
        location.href = "/teacher/Admin/admin"
    })
})

function excel(){
     layui.use('upload', function() {
        var upload = layui.upload;

        //执行实例
        var uploadInst = upload.render({
            elem: '#excel' //绑定元素
                ,
            url: '/teacher/Admin/excel' //上传接口
                ,
            accept: 'file',
            field: 'excel',
            before: function() {
                load = layer.load()
            },
            done: function(data) {
                console.log(data)
                layer.close(load)
                if (data['valid']) {
                    layer.msg(data['msg'],{
                    icon: 1, //提示的样式
                    time: 600, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        page();
                    }
                });
                } else {
                   layer.msg(data['msg'],{
                    icon: 2, //提示的样式
                    time: 600, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        location.href = "/teacher/Admin/admin"
                    }
                });
                }
            },
        });
    });
}

function page() {
    layui.use('laypage', function() {
        var laypage = layui.laypage;
        $.ajax({
            type: "get",
            url: '/teacher/Admin/admin',
            traditional: true,
            dataType: "json",
            success: function(data) {
                var len = data
                laypage.render({
                    elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                        ,
                    count: len, //数据总数，从服务端得到
                    limit: 4,
                    jump: function(obj, first) {
                        //obj包含了当前分页的所有参数，比如：
                        var data = { 'curr': obj.curr, 'limit': obj.limit };
                        $.ajax({
                            type: "post",
                            url: '/teacher/Admin/adminPage',
                            traditional: true,
                            dataType: "json",
                            data: data,
                            beforeSend: function(){
                                index = layer.load()
                            },
                            success: function(data) {

                                layer.close(index)


                              $("#tbody").empty();
                                var data_html = "";
                                if (!data.length > 0) {
                                    layer.confirm('还没有教师数据,请添加!', function() {
                                        layer.closeAll('dialog')
                                    });
                                } else {
                                    $.each(data, function(index, array) {
                                        data_html += html(array)
                                    });
                                }
                                $("#tbody").append(data_html);
                                $('.layui-unselect').not('.header').click(function() {
                                    $(this).toggleClass('layui-form-checked')
                                })

                                checkbox()
                            }
                        });
                    }
                });
            }
        })
    });
}

function checkbox() {
    $('.checkbox').click(function() {
        var checkbox = $('.checkbox')
        var flag = false;
        var arr = [];
        checkbox.each(function(item) {
            var c = $(this).attr('class')
            arr[item] = c
        })
        var flag = arr.every(function(item) {
            return item == "checkbox layui-unselect layui-form-checkbox layui-form-checked"
        })
        if (flag) {
            $('.header').addClass('layui-form-checked')
        } else {
            $('.header').removeClass('layui-form-checked')
        }

    })
}

function member_del(obj, id) {
    layer.confirm('确认要删除吗？', function(index) {
        tch_id = {};
        tch_id[0] = id
        $.ajax({
            type: "post",
            url: '/teacher/Admin/del',
            traditional: true,
            dataType: "json",
            data: tch_id,
            success: function(data) {
                layer.msg('删除成功', {
                    icon: 1, //提示的样式
                    time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        page();
                    }
                });
            }
        })
        // $(obj).parents("tr").remove();
        // layer.msg('已删除!', { icon: 1, time: 1000 });
    });
}

function delAll(argument) {
    var id = tableCheck.getData()
    if (!id.length) {
        layer.msg('请选择要删除的数据', {
            icon: 2, //提示的样式
            time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
            end: function() {
                layer.closeAll('dialog')
            }
        });
        return 0
    }
    layer.confirm('确认要删除吗？', function(index) {
        var id = tableCheck.getData()
        var tch_id = {};
        id.forEach(function(item, index) {
            tch_id[index] = item
        })
        $.ajax({
            type: "post",
            url: '/teacher/Admin/del',
            traditional: true,
            dataType: "json",
            data: tch_id,
            success: function(data) {
                layer.msg('删除成功', {
                    icon: 1, //提示的样式
                    time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        $('.header').removeClass('layui-form-checked')
                        page()
                    }
                });
            }
        })
    });
}

function search() {
    var info = $("#input").val();
    layui.use('laypage', function() {
        var laypage = layui.laypage;
        $.ajax({
            type: "post",
            url: '/teacher/Admin/checkLen',
            traditional: true,
            dataType: "json",
            data: {'search': info},
            success: function(data) {
                var len = data
                laypage.render({
                    elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                        ,
                    count: len, //数据总数，从服务端得到
                    limit: 4,
                    jump: function(obj, first) {
                        //obj包含了当前分页的所有参数，比如：
                        var info = $("#input").val();
                        var data = { 'curr': obj.curr, 'limit': obj.limit, 'search': info };
                        $.ajax({
                            type: "post",
                            url: '/teacher/Admin/check',
                            traditional: true,
                            dataType: "json",
                            data: data,
                            success: function(data) {
                                if (!data.length > 0) {
                                    layer.msg('没有您需要的数据', {
                                        icon: 2, //提示的样式
                                        time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                        end: function() {
                                            $('#input').val('')
                                            page();
                                        }
                                    });
                                }
                                $("#tbody").empty();
                                var data_html = "";
                                $.each(data, function(index, array) {
                                    data_html += html(array)
                                });


                                $("#tbody").append(data_html);
                            }
                        });
                    }
                });
            }
        })
    });
}

function html(array) {
    var html = null
    return html = `<tr>
            <td>
                <div class="checkbox layui-unselect layui-form-checkbox" lay-skin="primary" data-id="` + array['tch_id'] + `"><i class="layui-icon">&#xe605;</i></div>
            </td>
            <td>` + array['tch_numBer'] + `</td>
            <td>` + array['tch_name'] + `</td>
            <td>` + array['tch_phone'] + `</td>
            <td>` + array['tch_email'] + `</td>
            <td class="td-manage">
            <a title="负责班级" onclick="x_admin_show('` + array['tch_numBer'] + `负责的班级','/teacher/Admin/cla?id=` + array['tch_id'] + `')" href="javascript:;">
                <i class="layui-icon">&#xe63c;</i>
              </a>
                <a title="编辑教师信息" href="/teacher/Admin/edit?tch_id=` + array['tch_id'] + `">
                <i class="layui-icon">&#xe639;</i>
              </a>
                        <a title="删除" onclick="member_del(this,'` + array['tch_id'] + `')" href="javascript:;">
                <i class="layui-icon">&#xe640;</i>
              </a>
                    </td>
                </tr>`;
}