<?php
    class View_Helper_Carte
    {
        private $zoom;
        private $lat, $lon;
        private $overlays = array();
        private $markers = array();
        private $key_ign;
        private $interactive_layer = array();

        public function carte($lat, $lon, $markers = array(), array $size = array('height' => '100%'), $zoom  = 17)
        {
            // Récupération des couches
            $model_couchecarto = new Model_DbTable_CoucheCarto;
            $couches = $model_couchecarto->getList();
            if (count($couches) == 0) {
                return false;
            }

            // Récupération de la liste des couches intéractives
            $this->interactive_layer =$model_couchecarto->getInteractList();

            // Récupération de la clé IGN
            $key_ign = null;
            foreach ($couches as $couche) {
                if ($couche->ID_COUCHECARTOTYPE == 3) {
                    $key_ign = $couche->API_COUCHECARTO;
                }
            }
            $this->key_ign = $key_ign;

            if ($this->key_ign == null) {
                return false;
            }

            $this->buildContainer($size);
            $this->loadOverlays();
            $this->setZoom($zoom);
            $this->setCenter($lat, $lon);
            $this->setMarkers($markers);
            $this->load();

            return $this;
        }

        // Création du conteneur
        public function buildContainer($size)
        {
            echo '<script type="text/javascript" src="//api.ign.fr/geoportail/api/js/1.3/GeoportalExtended.js"></script>';
            echo '<div id="geo_container" style="height: ' . $size['height'] . '"></div>';
        }

        // Chargements des couches
        public function loadOverlays()
        {
            // On récupère les couches
            $model_couchecarto = new Model_DbTable_CoucheCarto;
            $rowset_couches = $model_couchecarto->getList();

            if(count($rowset_couches) == 0)

                return null;

            foreach ($rowset_couches as $row) {

                $this->overlays[$row->NOM_COUCHECARTOTYPE][] = array (
                    "name" => $row->NOM_COUCHECARTO,
                    "url" => $row->URL_COUCHECARTO,
                    "options" => array (
                        "params" => array (
                            "layers" => $row->LAYERS_COUCHECARTO,
                            "format" => $row->FORMAT_COUCHECARTO,
                            "transparent" => $row->TRANSPARENT_COUCHECARTO == 1 ? true : false,
                        ),
                        "options" => array (
                            "projection"=> "EPSG:4326",
                            "isBaseLayer" => $row->ISBASELAYER_COUCHECARTO == 1 ? true : false,
                            "visibility" => false
                        )
                    )
                );
            }
        }

        // Définition du zoom
        public function setZoom($zoom)
        {
            $this->zoom = $zoom;
        }

        // On positionne le centre de la carte aux coordonnées lat / lon
        public function setCenter($lat, $lon)
        {
            $this->lat = $lat;
            $this->lon = $lon;
        }

        // On load dans un tableau les marqueurs
        public function setMarkers($markers)
        {
            $this->markers = $markers;
        }

        // Chargement de la carte
        public function load()
        {
            echo '
                <script>
                    var map = Geoportal.load("geo_container",
                        ["'. $this->key_ign . '"],
                        {lat:  ' . $this->lat . ', lon:  ' . $this->lon . '},
                        ' . $this->zoom . ',
                        {
                            overlays: ' . Zend_Json::Encode($this->overlays) . ',
                            viewerClass: Geoportal.Viewer.Default,
                            onView: function() {
                                var markersLayer = new OpenLayers.Layer.Vector("Marqueurs non modifiables");
                                var draggableMarkersLayer = new OpenLayers.Layer.Vector("Marqueurs");

                                this.getViewer().getMap().addLayers([markersLayer, draggableMarkersLayer]);
                                var markers = ' . Zend_Json::Encode($this->markers) . ';

                                var drag = new OpenLayers.Control.DragFeature(draggableMarkersLayer);
                                this.getViewer().getMap().addControl(drag);
                                drag.deactivate();

                                for (var x in markers) {
                                    var style = {
                                        externalGraphic: markers[x].img,
                                        graphicWidth: 32,
                                        graphicHeight: 32,
                                        label: markers[x].label,
                                        fontColor: "white",
                                        fontSize: "16px",
                                        fontWeight: "bold"
                                    };
                                    var position = new OpenLayers.Geometry.Point(markers[x].lon, markers[x].lat);
                                    position.transform(OpenLayers.Projection.CRS84, this.getViewer().projection);

                                    if(markers[x].draggable == true)
                                        draggableMarkersLayer.addFeatures(new OpenLayers.Feature.Vector(position, null, style));
                                    else
                                        markersLayer.addFeatures(new OpenLayers.Feature.Vector(position, null, style));
                                }

                                var map = this.getViewer().getMap();

                                this.getViewer().getMap().setCursor("auto");
                                this.getViewer().setToolsPanelVisibility(false);

                                //callback_geo(this, drag);
                            }
                        }
                    );
                </script>
            ';
        }

        // Méthodes statiques
        public static function Marker($label, $lat, $lon, $draggable = false, $img = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png')
        {
            return array(
                "label" => $label,
                "lat" => $lat,
                "lon" => $lon,
                "draggable" => $draggable,
                "img" => $img
            );
        }
    }
