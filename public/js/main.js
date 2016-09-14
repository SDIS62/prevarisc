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
};

function initViewer(divId, ignKey, points, wmsLayers, onView) {
    if (points.length === 0) {
        return ;
    }
    
    var viewer = Geoportal.load(divId, ignKey, {
            lat: points[0].lat,
            lon: points[0].lon
        }, 17, OpenLayers.Util.extend({
            controls: [new Geoportal.Control.GraphicScale(), new Geoportal.Control.ToolBox()],
            language:'fr',
            displayProjection: 'EPSG:4326',
            proxy: '/proxy?url=',
            onView: function() {
                var map = viewer.getViewer().getMap();
                
                // Récupération de la toolbox pour l'identifiant
                var toolBox= viewer.getViewer().getMap().getControlsByClass('Geoportal.Control.ToolBox')[0];

                // Création de la barre de navigation
                map.addControl(new Geoportal.Control.NavToolbar({
                    // Div où la barre doit être ajoutée
                    div: OpenLayers.Util.getElement(toolBox.id+'_navbar'),
                    // Div où le resultat des mesures est affiché
                    targetElement: OpenLayers.Util.getElement(toolBox.id+'_navbar')
                })); 

                // Création de la barre de mesure
                map.addControl(new Geoportal.Control.MeasureToolbar({
                    // Div où la barre doit être ajoutée
                    div: OpenLayers.Util.getElement(toolBox.id+'_measure'),
                    // Div où le resultat des mesures est affiché
                    targetElement: OpenLayers.Util.getElement(toolBox.id+'_meares')
                }));

                // Création de la barre de zoom
                map.addControl(new Geoportal.Control.ZoomBar({
                    // Div où la barre doit être ajoutée
                    div: OpenLayers.Util.getElement(toolBox.id+'_zoombar'),
                    targetElement: OpenLayers.Util.getElement(toolBox.id+'_zoombar')
                }));
    
                // Création de la barre de zoom
                map.addControl(new Geoportal.Control.LayerSwitcher({
                    // Div où la barre doit être ajoutée
                    div: OpenLayers.Util.getElement(toolBox.id+'_layerswitcher'),
                    targetElement: OpenLayers.Util.getElement(toolBox.id+'_layerswitcher')
                }));
                
                // Ajout des couches WMS
                if (wmsLayers && wmsLayers.length > 0) {
                    $('#reponse-modal').dialog({
                        resizable: true,
                        title: 'WMS Information',
                        height: '500',
                        width: '800',
                        autoOpen: false,
                        close: function() {
                           $(this).empty(); 
                        },
                        modal: false}
                    );
                
                    for (var i = 0 ; i < wmsLayers.length ; i++) {
                        console.log(wmsLayers[i].URL_COUCHECARTO);
                        // ajout de la couche sur la carte
                        var layer = new OpenLayers.Layer.WMS(
                            wmsLayers[i].NOM_COUCHECARTO,
                            wmsLayers[i].URL_COUCHECARTO.replace('\{key\}', ignKey), {
                                layers: wmsLayers[i].LAYERS_COUCHECARTO,
                                format: wmsLayers[i].FORMAT_COUCHECARTO,
                                transparent: wmsLayers[i].TRANSPARENT_COUCHECARTO === 1 ? 'true' : 'false'
                            }, {
                                projection: 'EPSG:4326',
                                singleTile: false,
                                opacity: 1,
                                visibility: true
                            }
                        );

                        /*var infoFeature = new OpenLayers.Control.WMSGetFeatureInfo({
                            url: wmsLayers[i].URL_COUCHECARTO, 
                            title: 'Identify features by clicking',
                            layers: [layer],
                            queryVisible: true
                        });

                        infoFeature.events.register("getfeatureinfo", map, function(event) {
                            var $response = $(event.text);
                            var time = (new Date()).getTime();
                            $('#wms').append("<iframe id='wms-'>"+event.text+"</iframe>");
                        });*/
                        map.addLayer(layer);
                        //map.addControl(infoFeature);
                        //infoFeature.activate();
                    }
                }
                
                // Suppression des markers par défaut
                var vectorLayers = map.getLayersByClass('OpenLayers.Layer.Vector');
                if (vectorLayers.length > 0) {
                    var vectorLayer = vectorLayers[0];
                    if (vectorLayer.features.length > 0) {
                        vectorLayer.features[0].destroy();
                    }
                }
                
                // Ajout des POI avec les adresses sur la carte
                var epsg4326 =  new OpenLayers.Projection("EPSG:4326"); //WGS 1984 projection
                var projectTo = map.getProjectionObject(); //The map projection (Spherical Mercator)
                
                var vectorLayer = new OpenLayers.Layer.Vector("Etablissement");
    
                // Define markers as "features" of the vector layer:
                for (var i = 0 ; i < points.length ; i++) {
                    
                    var feature = new OpenLayers.Feature.Vector(
                        new OpenLayers.Geometry.Point(points[i].lon, points[i].lat).transform(epsg4326, projectTo),
                        {description:points[i].description} ,
                        {externalGraphic: '/images/red-dot.png', graphicHeight: 30, graphicWidth: 30, graphicXOffset:-15, graphicYOffset:-30  }
                    );    
                    vectorLayer.addFeatures(feature);
                }
                
                map.addLayer(vectorLayer);
    
                //Add a selector control to the vectorLayer with popup functions
                var controls = {
                  selector: new OpenLayers.Control.SelectFeature(vectorLayer, { onSelect: createPopup, onUnselect: destroyPopup })
                };

                function createPopup(feature) {
                  feature.popup = new OpenLayers.Popup.FramedCloud("pop",
                      feature.geometry.getBounds().getCenterLonLat(),
                      null,
                      '<div class="markerContent">'+feature.attributes.description+'</div>',
                      null,
                      true,
                      function() { controls['selector'].unselectAll(); }
                  );
                  //feature.popup.closeOnMove = true;
                  map.addPopup(feature.popup);
                }

                function destroyPopup(feature) {
                  feature.popup.destroy();
                  feature.popup = null;
                }

                map.addControl(controls['selector']);
                controls['selector'].activate();
                
                
                if (onView !== undefined) {
                    onView();
                }
            }
        })
    );
    
    return viewer;
    
}


function putMarkerAt(map, point, sourceProjection) {
    var vectorLayers = map.getLayersByClass('OpenLayers.Layer.Vector');
    if (vectorLayers.length > 0) {
        var vectorLayer = vectorLayers[0];
        for(var j = 0 ; j < vectorLayer.features.length ; j++) {
            vectorLayer.features[j].destroy();
        }
        var lonlat = point.transform(
            sourceProjection, vectorLayer.projection.toString()
        );
        point = new OpenLayers.Geometry.Point(lonlat.lon,lonlat.lat);
        vectorLayer.addFeatures([new OpenLayers.Feature.Vector(point, {}, {externalGraphic: '/images/red-dot.png', graphicHeight: 30, graphicWidth: 30, graphicXOffset:-15, graphicYOffset:-30  })]);
    }
}
