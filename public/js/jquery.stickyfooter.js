function stickyFooter() {

	$(window).unbind("DOMSubtreeModified");

	var footer = $("footer");

	if (footer.attr('style')) {
		footer.removeAttr('style');
	}	

	var pos = footer.position();
	var height = $(window).height();
	height = height - pos.top;
	height = height - footer.outerHeight();

	if (height > 0) {
		footer.css({'margin-top' : height+'px'});
		footer.css({'margin-bottom' : 0});
	}

	$(window).bind("DOMSubtreeModified", stickyFooter);
}

$(window).bind("load", stickyFooter);
$(window).bind("resize", stickyFooter);
$(window).bind("DOMSubtreeModified", stickyFooter);