function initGeoConceptKey(app_id, token) {
    GCUI.Settings = {app_id: app_id, token: token};
}
        
function initGeoConceptViewer(divId, url, layerName, points, wmsLayers, onView) {
    var map = new GCUI.Map(divId, {
        server: url,
        layer: layerName,
        scale : 3
    });
        
    /* Ajout des contrôles */
    map.addControl(new GCUI.Control.GraphicScale({
        posx : 0,
        posy : -1
    }));
    map.addControl(new GCUI.Control.LayerSwitcher());
    geoConceptGeocoder = new GCUI.Control.GeoCode();
    map.addControl(geoConceptGeocoder);
    
    /* Evènements au chargement */
    map.onEvent("load", function() {
        
        /* Ajout d'un layer avec les marqueurs des adresses */
        var sourceProjection =  new OpenLayers.Projection("EPSG:4326"); /* WGS 1984 projection */
        var mapProjection = map.getProjectionObject(); /* The map projection */

        var vectorLayer = new OpenLayers.Layer.Vector("Etablissement");

        // Define markers as "features" of the vector layer:
        for (var i = 0 ; i < points.length ; i++) {

            var feature = new OpenLayers.Feature.Vector(
                new OpenLayers.Geometry.Point(points[i].lon, points[i].lat).transform(sourceProjection, mapProjection),
                {description:points[i].description} ,
                {externalGraphic: '/images/red-dot.png', graphicHeight: 30, graphicWidth: 30, graphicXOffset:-15, graphicYOffset:-30  }
            );    
            vectorLayer.addFeatures(feature);
        }

        map.addLayer(vectorLayer);

        /* Ajout des couches WMS */
        if (wmsLayers && wmsLayers.length > 0) {
            for (var i = 0 ; i < wmsLayers.length ; i++) {
                // ajout de la couche sur la carte
                var wms = new OpenLayers.Layer.WMS(
                    wmsLayers[i].NOM_COUCHECARTO,
                    wmsLayers[i].URL_COUCHECARTO, {
                        layers: wmsLayers[i].LAYERS_COUCHECARTO,
                        format: wmsLayers[i].FORMAT_COUCHECARTO,
                        transparent: wmsLayers[i].TRANSPARENT_COUCHECARTO === 1 ? 'true' : 'false'
                    }, {
                        singleTile: false,
                        opacity: 1,
                        isBaseLayer : false,
                        visibility: true
                    }
                );

                map.addLayer(wms);
            }
        }

        /* Add a selector control to the vectorLayer with popup functions */
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

          map.addPopup(feature.popup);
        }

        function destroyPopup(feature) {
          feature.popup.destroy();
          feature.popup = null;
        }

        map.addControl(controls['selector']);
        controls['selector'].activate();
        
        /* On centre la carte sur le 1er point */
        var lonlat = new OpenLayers.LonLat(points[0].lon, points[0].lat);
        map.moveTo(lonlat.transform(sourceProjection, mapProjection));
        map.updateSize(); /* Important sinon on a un blink blanc au déplacement */

        if (onView !== undefined) {
            onView();
        }
    
    });
        
    return map;
}
    
function geoConceptGeocode(url, map, address, postalCode, city, callback) {
    geoConceptGeocoder.geocode({
        addressLine : address,
        postalCode : postalCode,
        city : city,
        callback : function(resp) {
            var result = null;
            if (resp.geocodedAddresses.length > 0) {
                var result = resp.geocodedAddresses[0];
                var lonlat = new OpenLayers.LonLat(result.x, result.y);
                var sourceProjection = new OpenLayers.Projection(result.projection);
                var mapProjection = new OpenLayers.Projection(map.getProjection());
                lonlat = lonlat.transform(sourceProjection, mapProjection);
                map.moveTo(lonlat, 8);
                geoConceptPutMarkerAt(map, lonlat);
                map.updateSize(); /* Important sinon on a un blink blanc au déplacement */
            }
            callback(result);
        },
        url : url
    });
}

function geoConceptPutMarkerAt(map, lonlat) {
    var vectorLayers = map.getLayersByClass('OpenLayers.Layer.Vector');
    if (vectorLayers.length > 0) {
        var vectorLayer = vectorLayers[0];
        for(var j = 0 ; j < vectorLayer.features.length ; j++) {
            vectorLayer.features[j].destroy();
        }
        point = new OpenLayers.Geometry.Point(lonlat.lon,lonlat.lat);
        vectorLayer.addFeatures([new OpenLayers.Feature.Vector(point, {}, {externalGraphic: '/images/red-dot.png', graphicHeight: 30, graphicWidth: 30, graphicXOffset:-15, graphicYOffset:-30  })]);
    }
}
