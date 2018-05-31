$(function() {
    page();
    $('.grade').change(function() {
        var arr = $('.grade');
        var data = {}
        $.each(arr, function(index, array) {
            data[index] = $(this).val();
        })
        layui.use('laypage', function() {
            var laypage = layui.laypage;
            $.ajax({
                type: "post",
                url: '/teacher/Admin/selectLen',
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
                            var arr = $('.grade');
                            var data = {};
                            $.each(arr, function(index, array) {
                                data[index] = $(this).val();
                            })
                            var info = { 'curr': obj.curr, 'limit': obj.limit };
                            var tchid = $('#allots').attr('data_tchid')
                            $.ajax({
                                type: "post",
                                url: '/teacher/Admin/selectData?curr=' + info['curr'] + '&limit=' + info['limit'],
                                traditional: true,
                                dataType: "json",
                                data: data,
                                success: function(data) {
                                    if (!data['data'].length > 0) {
                                        layer.msg('没有您需要的数据', {
                                            icon: 2, //提示的样式
                                            time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                        });
                                    }
                                    $("#tbody").empty();
                                    $("#specialty").empty();


                                    var data_html = "";
                                    $.each(data['data'], function(index, array) {
                                        data_html += html(array, tchid)
                                    });

                                    var specialty_html = `<option value=''>选择一个专业</option>`
                                    $.each(data['specialtys'], function(index, array) {
                                        if (data['specialty'] == array) {
                                            specialty_html += `<option value="`+array+`" class="option" selected="selected">`+array+`</option>`
                                        } else {
                                            specialty_html += `<option value="`+array+`" class="option">`+array+`</option>`
                                        }
                                    });


                                    $("#tbody").append(data_html);
                                    $("#specialty").append(specialty_html);

                                     $('.allot').click(function() {
                                        var class_id = $(this).attr('data-id')
                                        allot(this, class_id)
                                    })
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
    })
    $('#search').click(function() {
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

    $('#allots').click(function() {
        allots();
    })

    function page() {
        layui.use('laypage', function() {
            var laypage = layui.laypage;
            $.ajax({
                type: "get",
                url: '/teacher/Admin/classAllot',
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
                            var tchid = $('#allots').attr('data_tchid')
                            var data = { 'curr': obj.curr, 'limit': obj.limit };
                            $.ajax({
                                type: "post",
                                url: '/teacher/Admin/selectPage',
                                traditional: true,
                                dataType: "json",
                                data: data,
                                success: function(data) {
                                    $("#tbody").empty();
                                    var data_html = "";
                                    if (!data.length > 0) {
                                        layer.confirm('没有未分配的班级!', function() {
                                            // location.href="/teacher/Admin/addAdmin";\
                                            layer.closeAll('dialog')
                                            // x_admin_show('添加用户', 'addAdmin')
                                        });
                                    } else {
                                        $.each(data, function(index, array) {
                                            data_html += html(array, tchid);
                                        });
                                    }
                                    $("#tbody").append(data_html);

                                    $('.allot').click(function() {
                                        var class_id = $(this).attr('data-id')
                                        allot(this, class_id)
                                    })

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

    function search() {
        var info = $("#input").val();
        layui.use('laypage', function() {
            var laypage = layui.laypage;
            var data = { 'info': info }
            $.ajax({
                type: "post",
                url: '/teacher/Admin/searchLen',
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
                            var tchid = $('#allots').attr('data_tchid')
                            var info = $("#input").val();
                            var data = { 'curr': obj.curr, 'limit': obj.limit, 'search': info };
                            $.ajax({
                                type: "post",
                                url: '/teacher/Admin/searchCla',
                                traditional: true,
                                dataType: "json",
                                data: data,
                                success: function(data) {
                                    if (!data.length > 0) {
                                        layer.msg('没有您需要的数据', {
                                            icon: 2, //提示的样式
                                            time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                            end: function() {
                                                location.reload();
                                            }
                                        });
                                    }
                                    $("#tbody").empty();
                                    var data_html = "";
                                    $.each(data, function(index, array) {
                                        data_html += html(array)
                                    });


                                    $("#tbody").append(data_html)
                                     $('.allot').click(function() {
                                        var class_id = $(this).attr('data-id')
                                        allot(this, class_id)
                                    })
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

    function allots(argument) {
        var tchid = $('#allots').attr('data_tchid')
        var id = tableCheck.getData()
        if (!id.length) {
            layer.msg('请选择要添加的班级', {
                icon: 2, //提示的样式
                time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                end: function() {
                    layer.closeAll('dialog')
                }
            });
            return 0
        }
        layer.confirm('确认要添加吗？', function(index) {
            var id = tableCheck.getData()
            var class_id = {};
            id.forEach(function(item, index) {
                class_id[index] = item
            })
            $.ajax({
                type: "post",
                url: '/teacher/Admin/allot?id=' + tchid,
                traditional: true,
                dataType: "json",
                data: class_id,
                success: function(data) {
                    layer.msg('添加成功', {
                        icon: 1, //提示的样式
                        time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                        end: function() {
                            location.href = '/teacher/Admin/cla?id=' + tchid
                        }
                    });
                }
            })
        });
    }

    function allot(obj, id) {
        var class_id = id
        var tchid = $('#allots').attr('data_tchid')
        layer.confirm('确认要添加吗？', function(index) {
            $.ajax({
                type: "post",
                url: '/teacher/Admin/allot?id=' + tchid,
                traditional: true,
                dataType: "json",
                data: { 'class_id': class_id },
                success: function(data) {
                    layer.msg('添加成功', {
                        icon: 1, //提示的样式
                        time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                        end: function() {
                            location.href = '/teacher/Admin/cla?id=' + tchid
                        }
                    });
                }
            })

        });
    }

})

function html(array, tchid) {
    var html = null
    return html = `<tr>
        <td>
            <div class="checkbox layui-unselect layui-form-checkbox" lay-skin="primary" data-id="` + array['class_id'] + `"><i class="layui-icon">&#xe605;</i></div>
        </td>
        <td>` + array['class_grade'] + `</td>
        <td>` + array['class_name'] + `</td>
        <td>` + array['class_staffRoom'] + `</td>
        <td>` + array['class_specialty'] + `</td>
        <td class="td-manage">
        <a title="负责班级" href="/teacher/Checkc/index?tch_id=` + tchid + `&class_id=` + array['class_id'] + `">
            <i class="layui-icon">&#xe63c;</i>
          </a>
                    <a class="allot" title="添加" data-id=` + array['class_id'] + ` href="javascript:;">
            <i class="layui-icon">&#xe605;</i>
          </a>
                </td>
            </tr>`
}