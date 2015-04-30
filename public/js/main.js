document.addEventListener("DOMContentLoaded", function() {
    bindTitlePopup();
    bindContainerSize();
    bindEtsMarquee($(document));
    bindEtsPopup($(document));
}, false);

function bindTitlePopup() {
    // Titres
    $('a[title]').tipsy({live: true});
    $('abbr[title]').tipsy({live: true});
}

function bindContainerSize() {
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
}

function bindEtsMarquee($elem) {
    // Marquee sur les listes de recherche
    $elem.find('ul.recherche_liste li.etablissement').each(function() {
        var li_width = $(this).width();
        var left_width = $(this).find('.pull-left').width();
        var right_width = $(this).find('.pull-right').width();
        if( (left_width + right_width) > li_width) {
            var free_width = li_width - right_width - 20;
            $(this).find('.pull-left').css('width', free_width + 'px').css('overflow', 'hidden').marquee({
                duplicated: true,
                duration: 7500,
                pauseOnHover: true
            });
        }
    });
}

function bindEtsPopup($elem) {

    // Bulle ETS
    $elem.find('a[href^="/etablissement/index/id/"]').hoverIntent({
        over: function () {
            var id = $(this).attr('href').replace("/etablissement/index/id/", "");
            var e = $(this);
            e.popover({html: true, content: "<p class='text-center'><img src='/images/load.gif'></p>"}).popover('show');
            $.getJSON("/api/1.0/etablissement?id=" + id, function(data) {
                var ets_id = data.response.general.ID_ETABLISSEMENT;
                var ets_libelle = data.response.informations.LIBELLE_ETABLISSEMENTINFORMATIONS;
                var ets_genre = data.response.informations.LIBELLE_GENRE;
                var ets_type = data.response.informations.LIBELLE_TYPE_PRINCIPAL;
                var ets_statut = data.response.informations.LIBELLE_STATUT;
                var ets_cat = data.response.informations.ID_GENRE == 3 ? data.response.parents[0].LIBELLE_CATEGORIE : data.response.informations.LIBELLE_CATEGORIE;
                var ets_adresse = data.response.adresses[0];
                data.response.parents.forEach(function(element, index, array) {
                    array[index] = element.LIBELLE_ETABLISSEMENTINFORMATIONS;
                });
                var ets_parents = data.response.parents.join(' - ');
                data.response.adresses.forEach(function(element, index, array) {
                    array[index] = element.LIBELLE_COMMUNE;
                });

                if(data.response.informations.ID_GENRE == 1) {
                    var ets_adresses = "";
                    data.response.etablissement_lies.forEach(function(element, index, array) {
                        if (element.LIBELLE_COMMUNE_ADRESSE_DEFAULT != null && ets_adresses == "") {
                            ets_adresses = element.LIBELLE_COMMUNE_ADRESSE_DEFAULT;
                        }
                    });
                }
                else {
                    var ets_adresses = data.response.adresses.join(' - ');
                }

                html = "";
                if(ets_parents != '') html += "<span>" + ets_parents + "</span><br>";
                html += "<span class='lead'><strong>";
                if(ets_type != null) html+= "<img src='/images/types/b/icone-type-" + ets_type + ".png'>&nbsp;";
                html += ets_libelle + "</strong></span>";
                html += "&nbsp;<span><small>" + ets_adresses + "</small></span>";
                html += "<br /><span><small>#" + data.response.general.NUMEROID_ETABLISSEMENT + "</small></span>";

                if(data.response.presence_avis_differe === true) {
                    html += "<br><br><p class='avis' style='background-color: #3a87ad; font-size: .7em; float: none'>Avis différé</p>";
                }
                else if(data.response.avis == 1) {
                    html += "<br><br><p class='avis F' style='font-size: .7em; float: none'>Favorable" + (data.response.informations.ID_GENRE == 3 ? '' : ' à l\'exploitation') + "</p>";
                }
                else if(data.response.avis == 2) {
                    html += "<br><br><p class='avis D' style='font-size: .7em; float: none'>Défavorable" + (data.response.informations.ID_GENRE == 3 ? '' : ' à l\'exploitation') + "</p>";
                }
                else if(data.response.informations.ID_GENRE != 1) {
                    html += "<br><br><p class='avis' style='font-size: .7em; ; float: none'>Avis d'exploitation indisponible</p>";
                }

                if(ets_cat != null && ets_type != null) html += "<br><span>" + ets_cat + " - " + ets_type + "</span>";

                if(ets_adresse != null) html += "<br><span>" + (ets_adresse.NUMERO_ADRESSE == null ? '' : ets_adresse.NUMERO_ADRESSE) + " " + ets_adresse.LIBELLE_RUE + " " + ets_adresse.CODEPOSTAL_COMMUNE + " " + ets_adresse.LIBELLE_COMMUNE + "</span>";

                html += "<br><br>";
                html += "<a href='/etablissement/index/id/" + ets_id + "' class='btn btn-small btn-primary btn-block'>Voir la fiche</a>";

                e.popover('destroy');
                e.popover({html: true, content: html}).popover('show');
            });
        },
        out: function() {
            var e = $(this);
            if($('.popover:hover').length === 0 && $('a[href^="etablissement/index/id/"]:hover').length === 0) {
                e.popover('destroy');
            }
            else {
                $('.popover').mouseleave(function() {
                    e.popover('destroy');
                });
            }
        },
        interval: 1000,
        timeout: 500
    });
};

function loadBloc($bloc) {
    $bloc.find('.panel-body').show();
    $bloc.removeClass('empty').addClass('loading');
    $.ajax({
        type: 'post',
        url: "/index/bloc",
        data: {id: $bloc.attr('id')},
        success: function(data) {
            $bloc.find(".panel-body").html(data);
            $container.packery('fit', $bloc.get(0));
            bindEtsMarquee($bloc);
            bindEtsPopup($bloc);
            $bloc.removeClass('loading').addClass('loaded');
        }
    });
}
