$(function() {
    page();
    var searchdata = '';
    excel()
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
        searchdata = info;
        var class_id = $("#class_id").val()
        layui.use('laypage', function() {
            var laypage = layui.laypage;
            var data = { 'info': info, 'class_id': class_id }
            $.ajax({
                type: "post",
                url: '/teacher/Checkc/indexLen',
                traditional: true,
                dataType: "json",
                data: data,
                beforeSend: function() {
                    index = layer.load();
                },
                success: function(data) {
                    layer.close(index)

                    searchData = data

                    var len = data.length

                    laypage.render({
                        elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                            ,
                        count: len, //数据总数，从服务端得到
                        limit: 10,
                        jump: function(obj, first) {


                            if (obj.curr > 1) {

                                var start = obj.curr * obj.limit-obj.limit

                                var data = searchData.slice(start, obj.curr * obj.limit)
                            }

                            if (obj.curr == 1) {
                                var start = 0

                                var data = searchData.slice(start, start+obj.limit)
                            }


                            $("#tbody").empty();

                            var data_html = html(data)
                            

                            $("#tbody").append(data_html)
                            $('.layui-unselect').not('.header').click(function() {
                                $(this).toggleClass('layui-form-checked')
                            })
                            $('#input').val('')

                        }
                    });
                }
            })
        });
    }

    function excel() {
        var class_id = $("#class_id").val()
        layui.use('upload', function() {
            var upload = layui.upload;

            //执行实例
            var uploadInst = upload.render({
                elem: '#excel' //绑定元素
                    ,
                url: '/teacher/Checkc/excel?class_id='+class_id //上传接口
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
                                location.reload()
                            }
                        });
                    } else {
                        layer.msg(data['msg'], {
                            icon: 2, //提示的样式
                            time: 600, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                            end: function() {
                                location.reload()
                            }
                        });
                    }
                },
            });
        });
    }

   
function page(searchdata) {
        var class_id = $("#class_id").val()
        layui.use(['laypage', 'layer'], function() {
            var laypage = layui.laypage;
            $.ajax({
                type: "post",
                url: '/teacher/checkc/index',
                traditional: true,
                dataType: "json",
                data: { 'class_id': class_id, 'searchdata' : searchdata},
                beforeSend:function(XMLHttpRequest){
                    index = parent.layer.load(0, {
                    shade: [0.2, '#393D49']
                    });
                },
                success: function(data) {

                    parent.layer.close(index)

                    stuData = data

                    var len = data.length
                    laypage.render({
                        elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                            ,
                        count: len, //数据总数，从服务端得到
                        limit: 10,
                        jump: function(obj, first) {

                            if (obj.curr > 1) {

                                var start = obj.curr * obj.limit-obj.limit

                                var data = stuData.slice(start, obj.curr * obj.limit)
                            }

                            if (obj.curr == 1) {
                                var start = 0

                                var data = stuData.slice(start, start+obj.limit)
                            }

                             $("#tbody").empty();

                            var data_html = html(data)
                            

                            $("#tbody").append(data_html);
                            $('.layui-unselect').not('.header').click(function() {
                                $(this).toggleClass('layui-form-checked')
                            })

                            checkbox();


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


})

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
                        location.reload()
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
                        location.reload()
                    }
                });
            }
        })
    });
}

function html(data){
    var data_html = "";

    if (!data.length > 0) {
      $("#tbody").append('<td colspan="8" style="text-align: center;"> 暂时没有数据 </td>');
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
            <td>` + array['company_address'] + `</td>
            <td class="td-manage">
            <a title="学生信息" onclick="x_admin_show('修改信息','/teacher/Checkc/stuInfo?id=`+ array['stu_id'] +`')" href="javascript:;">
                <i class="layui-icon">&#xe63c;</i>
              </a>
                <a title="编辑学生信息" href="javascript:;" onclick="x_admin_show('修改信息','/teacher/Checkc/edit?stu_id=` + array['stu_id'] + `')">
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
            <td>` + array['company_address'] + `</td>
                <td class="td-manage">
                <a title="学生信息" href="/teacher/Checkc/stuInfo?id=` + array['stu_id'] + `">
                    <i class="layui-icon">&#xe63c;</i>
                </a>
                </td>
            </tr>`;
            });
        }

    }

    return data_html
}

