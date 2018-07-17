$(function(){
	editInfo()

	
})

var editInfo = function () {
	    $('.practiceInfo td').dblclick(function(){
	        // if ($(this).children('span').html() == '') {

	            var name = $(this).children('span').attr('data-name')

	            var parent = $(this).children('span')

	            old = $(this).children('span').html()

	            $(this).children('span').html('')
	            parent.append('<input class="layui-input" id="input" style="width: 60%;display: inline-block;height: 25px;"></input>')
	            parent.children().focus()

	            // var span = $(this)
	            $('#input').val(old)

	            $('#input').blur(function(){

                    var stu_id = $('#stu_id').val()

                    var company_id = $('#company_id').val()
                    $("#input").remove()

	                var val = $(this).val()

                    if (!val) {
                        layer.msg('编辑错误', {
                            icon: 2, //提示的样式
                            time: 600,
                            end: function(){
                                return 0;
                            }
                        });
                    } else {
                        $.ajax({
                            type: "post",
                            url: '/teacher/Checkc/editCompanyInfo',
                            traditional: true,
                            dataType: "json",
                            data: {'name': name, 'val' : val, 'stu_id' : stu_id, 'company_id' : company_id},
                            success: function(res) {

                                if (res['valid']) {
                                    layer.msg(res['msg'], {
                                        icon: 1, //提示的样式
                                        time: 600,
                                        end: function(){
                                            // parent.html(old)
                                            location.reload()
                                        }
                                    });
                                } else {
                                    layer.msg(res['msg'], {
                                        icon: 2, //提示的样式
                                        time: 600,
                                        end: function(){
                                            parent.html(old)
                                            // location.reload()
                                        }
                                    });
                                }

                            }
                        })

                    }



	            })

	    })

	    $('.stuInfo td').dblclick(function(){
	        // if ($(this).children('span').html() == '') {

	            var name = $(this).children('span').attr('data-name')

	            var parent = $(this).children('span')

	            old = $(this).children('span').html()

	            $(this).children('span').html('')
	            parent.append('<input class="layui-input" id="input" style="width: 60%;display: inline-block;height: 25px;"></input>')
	            parent.children().focus()

	            // var span = $(this)
	            $('#input').val(old)

	            $('#input').blur(function(){


	                var stu_id = $('#stu_id').val()
	                // var company_id = $('#company_id').val()
	                var val = $(this).val()

	                $("#input").remove()

	                $.ajax({
	                    type: "post",
	                    url: '/teacher/Checkc/editStuInfo',
	                    traditional: true,
	                    dataType: "json",
	                    data: {'name': name, 'val' : val, 'stu_id' : stu_id},
	                    success: function(res) {

	                        if (res['valid']) {
	                            layer.msg(res['msg'], {
	                                icon: 1, //提示的样式
	                                time: 600,
	                                end: function(){
	                                    // parent.html(old)
	                                    location.reload()
	                                }
	                            });
	                        } else {
	                            layer.msg(res['msg'], {
	                                icon: 2, //提示的样式
	                                time: 600,
	                                end: function(){
	                                    parent.html(old)
	                                    // location.reload()
	                                }
	                            });
	                        }

	                    }
	                })
	            })

	    })

	}