<?php
	class Zend_View_Helper_Carte {
		
		private $bool_markerlayerexists = false;
		
		public function Carte() {
			return $this;
		}
		
		public function initCarte($height = 350) {
			
			// On récupère les couches
			$model_couchecarto = new Model_DbTable_CoucheCarto;
			$rowset_couches = $model_couchecarto->getList();
			
			if(count($rowset_couches) == 0)
				return null;
			
			// On écrit du HTML + JS
			?>
			<div id="carte-content" style="height: <?php echo $height; ?>px"></div>

			<script src="/js/carto/OpenLayers.js"></script>
			
			<?php
			// Si il y a une couche IGN, on importe les lib
			foreach($rowset_couches as $row) {
				if($row->ID_COUCHECARTOTYPE == 3) {
					?>
					<script src="http://api.ign.fr/geoportail/api?v=1.2-e&amp;key=<?php echo $row->API_COUCHECARTO ?>&amp;includeEngine=false"></script>
					<script src="/js/carto/GeoportalExtended.js"></script>
					<?php
				}
			}
			?>

			<script>

				// Nouvel objet carte, paramètre : id du div contenant la carte
				var map = new OpenLayers.Map('carte-content');
				//map.events.register("changebaselayer", this, function(evt) {
					//alert(this.map.getProjectionObject());
				//});

				// On ajoute des contrôles
				map.addControl(new OpenLayers.Control.LayerSwitcher());

				// On ajoute des couches
				var couches = new Array();

			<?php
			
			// On ajoute les couches de la BDD
			foreach($rowset_couches as $row) {
				switch($row->ID_COUCHECARTOTYPE) {
					// WMS
					case 1:
						?>
						couches.push(
							new OpenLayers.Layer.WMS(
								"<?php echo htmlspecialchars($row->NOM_COUCHECARTO, ENT_QUOTES) ?>",
								"<?php echo htmlspecialchars($row->URL_COUCHECARTO, ENT_QUOTES) ?>",
								{
									layers: "<?php echo htmlspecialchars($row->LAYERS_COUCHECARTO, ENT_QUOTES) ?>",
									format: "<?php echo htmlspecialchars($row->FORMAT_COUCHECARTO, ENT_QUOTES) ?>",
									transparent: <?php echo $row->TRANSPARENT_COUCHECARTO == 1 ? "true" : "false" ?>
								}, {
									isBaseLayer: <?php echo $row->ISBASELAYER_COUCHECARTO == 1 ? "true" : "false" ?>,
									visibility: false
								}
							)
						);
						<?php
						break;
					// OSM
					case 2:
						?>
						couches.push(new OpenLayers.Layer.OSM());
						<?php
						break;
					// IGN
					case 3:
						?>
						var cat = new Geoportal.Catalogue(map, gGEOPORTALRIGHTSMANAGEMENT);
						var zon = cat.getTerritory('EUE');
						var couche = cat.getLayerParameters(zon, 'ORTHOIMAGERY.ORTHOPHOTOS:WMSC');
						couche.options.opacity = 1.0;
						couche.transitionEffect = 'resize';
						couche.options.isBaseLayer = true;
						couche.options["GeoRM"] = Geoportal.GeoRMHandler.addKey(
							gGEOPORTALRIGHTSMANAGEMENT.apiKey,
							gGEOPORTALRIGHTSMANAGEMENT[gGEOPORTALRIGHTSMANAGEMENT.apiKey[0]].tokenServer.url,
							gGEOPORTALRIGHTSMANAGEMENT[gGEOPORTALRIGHTSMANAGEMENT.apiKey[0]].tokenServer.ttl,
							map
						);
						var layer = new couche.classLayer('IGN', 'http://wxs.ign.fr/geoportail/wmsc', couche.params, couche.options);
						couches.push(layer);
						<?php
						break;
				}
			}
			
			?>
				// On ajoute les couches
				map.addLayers(couches);
				map.zoomToMaxExtent();
			</script>
			<?php
			
			return $this;
		}
		
		public function addMarker($lon, $lat, $callback = "") {
			
			if($this->bool_markerlayerexists === false) {
				echo "<script>var markers = new OpenLayers.Layer.Markers('Marqueurs'); map.addLayer(markers);</script>";
				$this->bool_markerlayerexists = true;
			}
			?>
			<script>
				var dimension_icon = new OpenLayers.Size(32, 32);
				var offset_icon = new OpenLayers.Pixel(-(dimension_icon.w/2), -dimension_icon.h);
				var icon = new OpenLayers.Icon('/images/red-dot.png', dimension_icon, offset_icon);
				
				var lonlat = new OpenLayers.LonLat(<?php echo $lon ?>, <?php echo $lat ?>).transform(
			        new OpenLayers.Projection("EPSG:4326"),
					map.getProjectionObject()
				);
					
				var marker = new OpenLayers.Marker(lonlat, icon);
				markers.addMarker(marker);
			</script>
			<?php
			
			return $this;
		}
		
		public function setCenter($lon, $lat, $zoom = 17) {
			echo "<script>map.setCenter(new OpenLayers.LonLat(".$lon.", ".$lat.").transform(OpenLayers.Projection.CRS84, map.getProjectionObject()), ".$zoom.");</script>";
			return $this;
		}
	}