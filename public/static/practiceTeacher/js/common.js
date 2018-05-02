$(function() {

    var __main = function() {
    	var src = cookies('src');
    	if (src) {
	    	$('#iframe').attr("src",src)
	    	cookies({ src: null });
    	}
    }
    __main();
})