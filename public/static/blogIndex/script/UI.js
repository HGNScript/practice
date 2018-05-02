/**
 * banner插件负责页面banner块的初始化和设置
 * @return {[type]}   [对象本身]
 */
(function($) {
  //定义命名空间
  var namespace = 'banner';
  //默认的操作对象
  var methods = {
    index: 0,
    timerName: null, //用来存储定时器
    /**
     * 将传入的对象进行初始化
     * @param  {[type]} options [用户插入的自定义属性]
     * @return {[type]}         [对象本身]
     */
    init: function(options) {
      /*合并 插件默认的对象和传入的对象 形成options参数对象*/
      options = $.extend({}, $.fn[namespace].defaults, options);
      this.css({
        position: "relative",
        height: options.H + 'px',
      })
      this.children().css({
        position: "absolute",
        width: "100%",
        height: "100%"
      })
      // console.log(this.children().not(this.children().first()))
      this.children('div').not(this.children().first()).css({
        "opacity": "0",
      })
      this.children("span").css({
        "position": "absolute",
        "width": options.btnW + 'px',
        "height": options.btnH + 'px',
        'top': (options.H / 2) - (options.btnH / 2) + 'px',
        'font-size': options.btnSize() + 'px',
        'text-align': 'center',
        'padding-top': 70 * .2 + 'px',
        'cursor': 'pointer',
        'opacity': 0
      }).hover(
        function() {
          $(this).parent().children('span').animate({
            'opacity': 1,
          }, 1000)
        },
        function() {
          $(this).parent().children('span').animate({
            'opacity': 0,
          }, 1000)
        }
      );
      this.children(".prve").on('click', function() {
        methods.prve(options, $(this).parent());
      });

      this.children(".next").css({
        'right': 0,
      }).on('click', function() {
        methods.next(options, $(this).parent());
      });
      //调用定时器
      methods.timer(options, this);

      this.hover(
        function() {
          clearInterval(methods.timerName);
        },
        function() {
          methods.timer(options, $(this));
          // console.log(methods.timer)
        })



      return this; //返回的是对象本身
    },
    /*点击prve时,根据模式调用相应的方法*/
    prve: function(options, banner) {
      if (options.way() == 'carousel') {
        console.log(1);
      } else if (options.way() == 'carouselTop') {
        console.log(2);
      } else {
        var len = banner.children('div').length;
        if (this.index <= 0) {
          this.index = len - 1;
          banner.children('div').not(banner.children('div').eq(this.index)).animate({
            "opacity": 0
          }, 1000);
          banner.children('div').eq(this.index).animate({
            "opacity": 1
          }, 1000);
          return false;

        }
        console.log(this.index);
        // console.log(banner.children('div').eq(this.index-1))
        banner.children('div').eq(this.index - 1).animate({
          'opacity': '1'
        }, 1000)
        banner.children('div').eq(this.index).animate({
          'opacity': '0'
        }, 1000)
      }
      this.index--;
    },
    /*点击next时,根据模式调用相应的方法*/
    next: function(options, banner) {
      if (options.way() == 'carousel') {
        console.log(1);
      } else if (options.way() == 'carouselTop') {
        console.log(2);
      } else {
        var len = banner.children('div').length;
        if (this.index >= len - 1) {
          this.index = 0;
          banner.children('div').not(banner.children('div').eq(this.index)).animate({
            "opacity": 0
          }, 1000);
          banner.children('div').eq(this.index).animate({
            "opacity": 1
          }, 1000);
          return false;

        }
        banner.children('div').eq(this.index + 1).animate({
          'opacity': '1'
        }, 1000)
        banner.children('div').eq(this.index).animate({
          'opacity': '0'
        }, 1000)
      }
      this.index++;
    },
    /**
     * 定时器
     * @param  {[type]} options [参数对象]
     * @param  {[type]} banner  [使用的dom元素]
     * @return {[type]}         [description]
     */
    timer: function(options, banner) {
      this.timerName = setInterval(function() {
        methods.next(options, banner);
      }, options.waitTime)
    }
  };
  /*如果传进来的参数在对象methods中存在,*/
  $.fn[namespace] = function(method) {
    if (methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));

    } else if ($.type(method) === 'object') {

      return methods.init.apply(this, arguments);

    } else {

      $.error('Method' + method + ' does not exist!');
    }
  };
  //默认值
  $.fn[namespace].defaults = {
    overTime: 2000, //图片过度时间
    waitTime: 2000, //停止时间
    H: 400, //banner高度
    btnH: 100, //按钮高度
    btnW: 50, //按钮宽度
    btnSize: function() { //按钮字体大小
      return this.btnH * 0.7;
    },
    fadeinout: true, //默认的方式(淡入淡出)
    carousel: false, //轮播
    carouselTop: false, //上下轮播
    way: function() { //判断用户使用的方式
      if (this.carousel) {
        return 'carousel';
      } else if (this.carouselTop) {
        return 'carouselTop';
      } else {
        return 'fadeinout';
      }
    }
  };
})(jQuery);

