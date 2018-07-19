$(function(){


	layui.use('layer', function(){
	  var layer = layui.layer;
	  
	});              
     

	var authority = $('#hidn').val();
    var staffRoom = $('#staffRoom').val();

    if (staffRoom) {
        page(null)
        search();
    }

    if (authority == 1) {

        // page();
        if (staffRoom) {
            $.ajax({
                type: "get",
                url: '/teacher/Statistics/proportion?class_staffRoom='+staffRoom,
                traditional: true,
                dataType: "json",
                success: function(data) {
                    postChart(data)
                }
            })
        } else {
            $.ajax({
                type: "get",
                url: '/teacher/Statistics/proportion',
                traditional: true,
                dataType: "json",
                success: function(data) {
                    postChart(data)
                }
            })
        }
    } else {

        stuPage(null)
        stuSearch()

          $.ajax({
                type: "get",
                url: '/teacher/Statistics/proportion',
                traditional: true,
                dataType: "json",
                success: function(data) {
                    postChart(data)
                }
            })
    }

function postChart(data) {
    var myChart1 = echarts.init(document.getElementById('echarts_post1'));
    var myChart2 = echarts.init(document.getElementById('echarts_post2'));
    option1 = {
        title: {
            text: '签到率数据',
            x: 'center'
        },
        series: [{
            name: '',
            type: 'pie',
            radius: ['40%', '55%'],
            label: {
                normal: {
                    formatter: '{a|{a}}{abg|}\n{hr|}\n{b|{b}：}{c}({per|{d}%)}  ',
                    rich: {
                        b: {
                            fontSize: 13,
                            lineHeight: 33
                        },
                    }
                }
            },
            data: [
                { value: data['signin'], name: '已签到人数' },
                { value: data['unSignin'], name: '未签到人数' },
            ]
        }]
    };


    option2 = {
        title: {
            text: '日志数据',
            x: 'center'
        },
        series: [{
            name: '',
            type: 'pie',
            radius: ['40%', '55%'],
            label: {
                normal: {
                    formatter: '{a|{a}}{abg|}\n{hr|}\n{b|{b}：}{c}({per|{d}%)}  ',
                    rich: {
                        b: {
                            fontSize: 13,
                            lineHeight: 33
                        },
                    }
                }
            },
            data: [
                { value: data['logs'], name: '日志已填写人数' },
                { value: data['unLogs'], name: '日志未填写人数' },
            ]
        }]
    };


    myChart1.setOption(option1);
    myChart2.setOption(option2);


}


classData = null
stuData = null

function page(search) {
    var staffRoom = $('#staffRoom').val();
    layui.use(['laypage', 'layer'], function() {
        var laypage = layui.laypage;
        $.ajax({
            type: "post",
            url: '/teacher/Statistics/index?class_staffRoom='+staffRoom,
            traditional: true,
            dataType: "json",
            data: search,
            beforeSend: function(){
                index = layer.load()
            },
            success: function(data) {
                layer.closeAll()

                classData = data

                var len = classData.length

                laypage.render({
                    elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                        ,
                    count: len, //数据总数，从服务端得到
                    limit: 10,
                    jump: function(obj, first) {

                        if (obj.curr > 1) {

                            var start = obj.curr * obj.limit-obj.limit

                            var data = classData.slice(start, obj.curr * obj.limit)
                        }

                        if (obj.curr == 1) {
                            var start = 0

                            var data = classData.slice(start, start+obj.limit)
                        }

                        $("#tbody").empty();
                                var data_html = "";
                                $.each(data, function(index, array) {
                                    data_html += `<tr>
                                    <td>` + array['class_name'] + `</td>
                                    <td>` + array['signin'] + `</td>
                                    <td>` + array['unSignin'] + `</td>
                                    <td>` + array['logs'] + `</td>
                                    <td>` + array['unLogs'] + `</td>
                                    <td>` + array['sum'] + `</td>
                                        </tr>`;
                                });

                        $("#tbody").append(data_html);
                    }
                });
            }
        })
    });
}

function search(){
    $('#input').keypress(function(event) {
        var keynum = (event.keyCode ? event.keyCode : event.which);
        if (keynum == '13') {
            var val = $('#input').val();
            if (!val) {
                layer.msg('请填写您要查询的数据', {
                    icon: 2, //提示的样式
                    time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        layer.closeAll('dialog')
                    }
                });
            } else {
                var search = $('#input').val()
                var data = {'search': search}
                page(data)
                $('#input').val('');
            }
    }
});

$('#search').click(function() {
    var val = $('#input').val();
    if (!val) {
            layer.msg('请填写您要查询的数据', {
                icon: 2, //提示的样式
                time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                end: function() {
                    layer.closeAll('dialog')
                }
            });
        } else {
            var search = $('#input').val()
            var data = {'search': search}

            page(data)
            $('#input').val('');
        }
    })
}

function stuPage(search) {
    layui.use(['laypage', 'layer'], function() {
        var laypage = layui.laypage;
        $.ajax({
            type: "post",
            url: '/teacher/Statistics/allStuInfo',
            traditional: true,
            dataType: "json",
            data: search,
            beforeSend: function(){
                index = layer.load()
            },
            success: function(data) {
                layer.closeAll()

                stuData = data

                var len = stuData.length

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

                        $("#tbody1").empty();
                                var data_html = "";
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
                <a title="学生信息" onclick="x_admin_show('修改信息','/teacher/Checkc/stuInfo?id=`+ array['stu_id'] +`')"  href="javascript::">
                    <i class="layui-icon">&#xe63c;</i>
                </a>
                </td>
                                        </tr>`

                                        ;
                                });

                        $("#tbody1").append(data_html);
                    }
                });
            }
        })
    });
}

function stuSearch(){
    $('#input2').keypress(function(event) {
        var keynum = (event.keyCode ? event.keyCode : event.which);
        if (keynum == '13') {
            var val = $('#input2').val();
            if (!val) {
                layer.msg('请填写您要查询的数据', {
                    icon: 2, //提示的样式
                    time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                    end: function() {
                        layer.closeAll('dialog')
                    }
                });
            } else {
                var search = $('#input2').val()
                var data = {'search': search}
                stuPage(data)
                $('#input2').val('');
            }
    }
});

$('#search2').click(function() {
    var val = $('#input2').val();
    if (!val) {
            layer.msg('请填写您要查询的数据', {
                icon: 2, //提示的样式
                time: 1000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                end: function() {
                    layer.closeAll('dialog')
                }
            });
        } else {
            var search = $('#input2').val()
            var data = {'search': search}

            stuPage(data)
            $('#input2').val('');
        }
    })
}




})