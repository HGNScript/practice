$(function() {
    excel()
    page();
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

    $('#search').click(function() {
        search()
    })

    function search() {
        var info = $("#input").val()
        var class_name = $("#class_name").val()
        layui.use('laypage', function() {
            var laypage = layui.laypage;
            var data = { 'info': info, 'class_name': class_name }
            $.ajax({
                type: "post",
                url: '/teacher/Checkc/indexLen',
                traditional: true,
                dataType: "json",
                data: data,
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
                            var class_name = $("#class_name").val()
                            var data = { 'curr': obj.curr, 'limit': obj.limit, 'class_name': class_name, 'info': info };
                            $.ajax({
                                type: "post",
                                url: '/teacher/Checkc/searchStu',
                                traditional: true,
                                dataType: "json",
                                data: data,
                                success: function(data) {
                                    if (!data.length > 0) {
                                        layer.msg('没有您需要的数据', {
                                            icon: 2, //提示的样式
                                            time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                            end: function() {
                                                // location.reload();
                                            }
                                        });
                                    }
                                    $("#tbody").empty();
                                    var data_html = "";
                                    if ($('#hidn').val() == 2) {
                                        $.each(data, function(index, array) {
                                            data_html += `<tr>
                                             <td>
                                        <div class="checkbox layui-unselect layui-form-checkbox" lay-skin="primary" data-id="` + array['stu_id'] + `"><i class="layui-icon">&#xe605;</i></div>
                                    </td>
                                    <td>` + array['stu_numBer'] + `</td>
                                    <td>` + array['stu_name'] + `</td>
                                    <td>` + array['stu_phone'] + `</td>
                                    <td>` + array['signInFlag'] + `</td>
                                    <td>` + array['logsFlag'] + `</td>
                                    <td>` + array['company_name'] + `</td>
                                    <td class="td-manage">
                                   <a title="学生信息" href="/teacher/Checkc/stuInfo?id=` + array['stu_id'] + `">
                                        <i class="layui-icon">&#xe63c;</i>
                                      </a>
                                        <a title="编辑学生信息" href="/teacher/Checkc/edit?stu_id=` + array['stu_id'] + `">
                                        <i class="layui-icon">&#xe639;</i>
                                      </a>
                                         <a title="删除" onclick="member_del(this,'` + array['stu_id'] + `')" href="javascript:;">
                                            <i class="layui-icon">&#xe640;</i>
                                          </a>
                                            </td>
                                        </tr>`;
                                        });
                                    } else {
                                        $.each(data, function(index, array) {
                                            data_html += `<tr>
                                    <td>` + array['stu_numBer'] + `</td>
                                    <td>` + array['stu_name'] + `</td>
                                    <td>` + array['stu_phone'] + `</td>
                                    <td>` + array['signInFlag'] + `</td>
                                    <td>` + array['logsFlag'] + `</td>
                                    <td>` + array['company_name'] + `</td>
                                    <td class="td-manage">
                                    <a title="学生信息" href="/teacher/Checkc/stuInfo">
                                        <i class="layui-icon">&#xe63c;</i>
                                      </a>
                                            </td>
                                        </tr>`;
                                        });
                                    }


                                    $("#tbody").append(data_html)
                                    $('.layui-unselect').not('.header').click(function() {
                                        $(this).toggleClass('layui-form-checked')
                                    })
                                    $('#input').val('')
                                }
                            });
                        }
                    });
                }
            })
        });
    }
    function excel() {
    var class_name = $("#class_name").val()
    layui.use('upload', function() {
        var upload = layui.upload;

        //执行实例
        var uploadInst = upload.render({
            elem: '#excel' //绑定元素
                ,
            url: '/teacher/Checkc/excel?class_name='+class_name //上传接口
                ,
            accept: 'file',
            field: 'excel',
            before: function() {
                load = layer.load()
            },
            done: function(data) {
                layer.close(load)
                if (data['valid']) {
                    layer.msg(data['msg'], {
                        icon: 1, //提示的样式
                        time: 600, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                        end: function() {
                            page()
                        }
                    });
                } else {
                    layer.msg(data['msg'], {
                        icon: 2, //提示的样式
                        time: 600, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                        end: function() {
                            page()
                        }
                    });
                }
            },
        });
    });
}


})

function page() {
        var class_name = $('#class_name').val()
        layui.use('laypage', function() {
            var laypage = layui.laypage;
            $.ajax({
                type: "post",
                url: '/teacher/checkc/index',
                traditional: true,
                dataType: "json",
                data: { 'class_name': class_name },
                success: function(data) {
                    var len = data
                    laypage.render({
                        elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                            ,
                        count: len, //数据总数，从服务端得到
                        limit: 4,
                        jump: function(obj, first) {
                            //obj包含了当前分页的所有参数，比如：
                            var info = { 'curr': obj.curr, 'limit': obj.limit };
                            // console.log(data);
                            $.ajax({
                                type: "post",
                                url: '/teacher/Checkc/indexPage?curr=' + info['curr'] + '&limit=' + info['limit'],
                                traditional: true,
                                dataType: "json",
                                data: { 'class_name': class_name },
                                success: function(data) {
                                    console.log(data)
                                    $("#tbody").empty();
                                    var data_html = "";
                                    if (!data.length > 0) {
                                        layer.confirm('还没有学生数据,请添加!', function() {
                                            // location.href="/teacher/Admin/addAdmin";\
                                            layer.closeAll('dialog')
                                            // x_admin_show('添加用户', 'addAdmin')
                                        });
                                    } else {
                                        if ($('#hidn').val() == 2) {
                                            $.each(data, function(index, array) {
                                                data_html += `<tr>
                                            <td>
                                        <div class="checkbox layui-unselect layui-form-checkbox" lay-skin="primary" data-id="` + array['stu_id'] + `"><i class="layui-icon">&#xe605;</i></div>
                                    </td>
                                    <td>` + array['stu_numBer'] + `</td>
                                    <td>` + array['stu_name'] + `</td>
                                    <td>` + array['stu_phone'] + `</td>
                                    <td>` + array['signInFlag'] + `</td>
                                    <td>` + array['logsFlag'] + `</td>
                                    <td>` + array['company_name'] + `</td>
                                    <td class="td-manage">
                                    <a title="学生信息" href="/teacher/Checkc/stuInfo?id=` + array['stu_id'] + `">
                                        <i class="layui-icon">&#xe63c;</i>
                                      </a>
                                        <a title="编辑学生信息" href="/teacher/Checkc/edit?stu_id=` + array['stu_id'] + `">
                                        <i class="layui-icon">&#xe639;</i>
                                      </a>
                                              <a title="删除" onclick="member_del(this,'` + array['stu_id'] + `')" href="javascript:;">
                                            <i class="layui-icon">&#xe640;</i>
                                          </a>
                                            </td>
                                        </tr>`;
                                            });
                                        } else {
                                            $.each(data, function(index, array) {
                                                data_html += `<tr>
                                    <td>` + array['stu_numBer'] + `</td>
                                    <td>` + array['stu_name'] + `</td>
                                    <td>` + array['stu_phone'] + `</td>
                                    <td>` + array['signInFlag'] + `</td>
                                    <td>` + array['logsFlag'] + `</td>
                                    <td>` + array['company_name'] + `</td>
                                    <td class="td-manage">
                                    <a title="学生信息" href="/teacher/Checkc/stuInfo?id=` + array['stu_id'] + `">
                                        <i class="layui-icon">&#xe63c;</i>
                                  </a>
                                            </td>
                                        </tr>`;
                                            });
                                        }
                                    }
                                    $("#tbody").append(data_html);
                                    $('.layui-unselect').not('.header').click(function() {
                                        $(this).toggleClass('layui-form-checked')
                                    })

                                    checkbox();
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
            // checkbox.forEach(function(item, index){
            //     console.log(item)
            // })
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
        var class_id = {};
        id.forEach(function(item, index) {
            class_id[index] = item
        })
        $.ajax({
            type: "post",
            url: '/teacher/Checkc/del',
            traditional: true,
            dataType: "json",
            data: class_id,
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

function member_del(obj, id) {
    var stu_id = {}
    layer.confirm('确认要删除吗？', function(index) {
        stu_id[0] = id
        $.ajax({
            type: "post",
            url: '/teacher/Checkc/del',
            traditional: true,
            dataType: "json",
            data: stu_id,
            success: function(data) {
                if (true) {}
                layer.msg('删除成功', {
                    icon: 1, //提示的样式
                    time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        page();
                    }
                });
            }
        })
    });
}
