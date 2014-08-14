(function($){$.fn.toggleText=function(text){this.each(function(){$(this).focus(function(){if($(this).val()==text)
$(this).val("");$(this).css("color","black");}).blur(function(){if($(this).val().length==0)
$(this).val(text);$(this).css("color","gray");});$(this).blur();});};})(jQuery);