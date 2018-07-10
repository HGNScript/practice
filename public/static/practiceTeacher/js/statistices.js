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




function page(search) {
    var staffRoom = $('#staffRoom').val();
    layui.use('laypage', function() {
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

                var len = data
                laypage.render({
                    elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                        ,
                    count: len, //数据总数，从服务端得到
                    limit: 4,
                    jump: function(obj, first) {
                        //obj包含了当前分页的所有参数，比如：
                        var info = { 'curr': obj.curr, 'limit': obj.limit };
                        $.ajax({
                            type: "post",
                            url: '/teacher/Statistics/index?class_staffRoom='+staffRoom + '&limit=' + info['limit']+ '&curr=' + info['curr'],
                            traditional: true,
                            dataType: "json",
                            data: search,
                            beforeSend: function(){
                                index = layer.load()
                            },

                            success: function(data) {
                                layer.closeAll()
                                
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
})