$(function() {
	var log = console.log.bind(console)

	var topInitia = function() {
		var windowH = $(window).height();
		var topHW = 40;
		$('#top').css({
			'width': topHW + 'px',
			'height': topHW + 'px',
			'display': 'none',
			'position': 'fixed',
			'right': (topHW / 2),
			'top': windowH - topHW - (topHW / 2),
			'border-radius': '5px',
		})
	}
	var search = function() {
		$(".search input").focus(function() {
			$(".search").css({
				"box-shadow": "0px 0px 10px #1C2324"
			})
		})

		$(".search input").blur(function() {
			$(".search").css({
				"box-shadow": "none"
			})
		})
	}
	var top = function() {
		topInitia();

		$(window).resize(function() {
			topInitia();
		})
		//返回顶部
		$(window).scroll(function() {
			if ($(window).scrollTop() > 100) {
				$('#top').fadeIn(1000);
			} else {
				$('#top').fadeOut(1000);
			}
		})

		$('#top').click(function() {
			$('body,html').animate({
				scrollTop: 0
			}, 600);
			return false;
		})

	}
	var fontSize = function() {
		var e = document.documentElement
		var resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize'
		recalc = function() {
			var clientWidth = e.clientWidth
			if (!clientWidth) return
			if (clientWidth >= 1366) {
				e.style.fontSize = '100px'
			} else {
				e.style.fontSize = 100 * (clientWidth / 1366) + 'px'
			}
		}
		if (!document.addEventListener) return
		window.addEventListener(resizeEvt, recalc, false)
		window.addEventListener('DOMContentLoaded', recalc, false)
		recalc();
	}

	var navBar = function() {
		$('.navbar-toggle').hover(function() {
			$(this).css({
				'background': '#1C2324'
			}, )
		}, function() {
			$(this).css({
				'background': 'none'
			}, )
		})
	}

	var list = function() {
		var lis = $('.nav-list li span').parent()
		var colorArr = [];
		lis.each(function(i) {
			colorArr.push($(lis[i]).css('background-color'))
		});

		$('.nav-list li span').each(function(j) {
			$(this).css({
				'border-color': colorArr[j],
				'border-bottom-color': 'transparent',
			})
		})

		$('.nav-list li').hover(function() {
			$('span', this).not('.active span').css({
				'transition': '.2s',
				'border-top-width': '20px',
				'border-bottom-width': '40px',
			})
		}, function() {
			$('span', this).not('.active span').css({
				'border-top-width': '0px',
				'border-bottom-width': '0px',
			})
		})
	}

	var _main = function() {
		$('#banner').banner("init", {
			time: 3000,
			waitTime: 4000,
			H: 200
		});
		search()
		top()
		fontSize()
		navBar()
		list()

	}
	_main();

})