document.addEventListener("DOMContentLoaded", function() {
    // Titres
    $('a[title]').tipsy({live: true});
    $('abbr[title]').tipsy({live: true});


    if ($('.ios_menu_style').is(':visible') > 0) {
        $('.main-container-fluid').css("width","80%");
        $('.main-container-fluid').css("display","table-cell");
    }
    else {
        $('.main-container-fluid').css("width","100%");
        $('.main-container-fluid').css("display","block");
    }

    $('.menu-trigger').click(function() {
        if ($('.ios_menu_style').is(':visible') > 0) {
            $('.ios_menu_style').hide();
            $('.main-container-fluid').css("width","100%");
            $('.main-container-fluid').css("display","block");
        }
        else {
            $('.ios_menu_style').show();
            $('.main-container-fluid').css("width","80%");
            $('.main-container-fluid').css("display","table-cell");
        }

    });

    // Marquee sur les listes de recherche
    $('ul.recherche_liste li.etablissement').each(function() {
        var li_width = $(this).width();
        var left_width = $(this).find('.pull-left').width();
        var right_width = $(this).find('.pull-right').width();
        if( (left_width + right_width) > li_width) {
            var free_width = li_width - right_width - 20;
            $(this).find('.pull-left').css('width', free_width + 'px').css('overflow', 'hidden').marquee({
                duplicated: true,
                duration: 7500
            });
        }
    });

}, false);
