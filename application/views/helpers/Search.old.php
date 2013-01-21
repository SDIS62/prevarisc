<?php

	class Zend_View_Helper_Search extends Zend_View_Helper_Abstract
	{
	
		public function search( $input, $niveau = 0 )
		{
			// Si il y a un résultat
			if( count($input[0]) > 0)
			{
			
				if( $niveau == 0 )
				{
					// Début du module
					?>
						<script type='text/javascript'>
							$('document').ready
							(
								function()
								{
									// On change le style du curseur
									$('.sous-parent').css('cursor','pointer');
									$('.parent').css('cursor','pointer');
									
									// Action toggle sur la classe parent
									$('.parent').toggle
									(
										function()
										{
											$(this).next().slideDown();
											$(this).children('span').last().children('img').attr('src','/images/template/icons/deplie.png');			
										},
										function()
										{
											$(this).next().slideUp();
											$(this).children('span').last().children('img').attr('src','/images/template/icons/replie.png');
										}
									);

									// Action toggle sur la classe sous-parent
									$('.sous-parent').toggle
									(
										function() // On ouvre 
										{
											$(this).next().slideDown();
											$(this).children('span').last().children('img').attr('src','/images/template/icons/deplie.png');
										},
										function() // on ferme
										{
											$(this).next().slideUp();
											$(this).children('span').last().children('img').attr('src','/images/template/icons/replie.png');
										}
									);
									
									// Action toggle sur le lien 'tout déplier'
									$('#tout_deplier').toggle
									(
										function() // On ouvre le tout
										{
											$('.child').slideDown();
											$('.sous-child').slideDown();
											$("img[src='/images/teplate/icons/']").attr('src','/images/template/icons/deplie.png');
										},
										function() // On ferme le tout
										{
											$('.child').slideUp();
											$('.sous-child').slideUp();
											$("img[src='/images/template/icons/deplie.png']").attr('src','/images/template/icons/replie.png');
										}
									);
								}
							);
						</script>

						<!-- ICI COMMENCE LE MODULE -->
						<hr class='clear' />
						<div id='results_container' class='grid_16'>
						
						<!-- Ici commence le résumé -->
						<div id='resume' class='grid_5 alpha push_11'>
							<h3>Résumé:</h3>
							<p>Vous recherchez actuellement :<br/>
							<?php if(isset($input[1]["ville"])) echo "dans la ville de <span class='highlight'>".$input[1]["ville"]."</span>"; else echo "dans <span class='highlight'>toutes</span> les villes"; ?><br/>
							<?php if(isset($input[1]["categories"])) echo ((count($input[1]["categories"])>1)?'les catégories':'la catégorie')." <span class='highlight'>".implode(", ", $input[1]["categories"])."</span>"; else echo "<span class='highlight'>toutes</span> les catégories"; ?><br/>
							<?php if(isset($input[1]["types"])) echo ((count($input[1]["types"])>1)?'les types':'Le type')." <span class='highlight'>".implode(", ", $input[1]["types"])."</span>."; else echo "<span class='highlight'>tous</span> les types";  ?>
							</p>
							
							<p><strong><?php echo $input[1]["count"] ?></strong> résultats<br/>
							<!-- (<strong>15</strong> sites, <strong>10</strong> ERP et <strong>18</strong> cellules) -->
							</p>
							
							<?php
							if( $input[1]["batiment"] != "site" )
							{
							?>
							
							<p>
							<?php
							// Avis
							foreach( $input[1]["avis"] as $value )
								echo "<strong>".$value[1]."</strong> avis \"".$value[0]."\"<br/>";
							?>
							</p>
							
							<p>
							<?php
							// Statut
							foreach( $input[1]["statut"] as $value )
								echo "<strong>".$value[1]."</strong> ".$input[1]["batiment"].( ( $value[1]>1 ) ? 's' : '' )." ".$value[0]."<br/>";
							?>
							</p>
							
							<?php
							}
							?>
							
							<p>
							<a href='javascript:void(0);'><img src='/images/template/icons/printer.png' width='16' height='16' alt='Print' />Imprimer la recherche</a>
							</p>
						</div>
						<!-- Ici finit le résumé -->
						
					<?php
						echo "
						<!-- Ici commence la liste -->
						<div id='liste' class='grid_11 omega pull_5' style='padding-top: 2.5em;'>
						
					";
					//<p id='depli-cont' class='grid_11'><a href='javascript:void(0);' id='tout_deplier'>Tout déplier</a></p>
				}
				else if( $niveau == 1 )
					echo "<div class='child'>";
				else if( $niveau == 2 )
					echo "<div class='sous-child'>";
				
				// Affichage des resultats
				foreach($input[0] as $value)
				{
					// Site
					if( isset($value["ID_SITE"]) && !isset($value["ID_ETABLISSEMENT"]) )
					{
					
						$search = new Model_DbTable_Search;
						$search	->setBatiment("etablissement");
						$search	->in($value["ID_SITE"]);
						$liste = $search->run();
						
						$bool = (count($liste[0])>0)?true:false;
					
						echo "
							<div class='etablissement ". ( ($bool)?(($niveau==0)?"parent":"sous-parent"):"" ) ." '>
								<span class='type'>SITE</span>
								<span class='nom-etablissement'><a href='/site/index/id/".$value["ID_SITE"]."' onclick=\"document.location.href='/site/index/id/".$value["ID_SITE"]."';\">".$value["LIBELLE"]."</a></span>
								<span class='ville'>".( ($value["LIBELLE_COMMUNE"])?$value["LIBELLE_COMMUNE"]:"")."</span>
								<span class='avis F'>F</span>
								".(( $bool )?"<span class='fleche'><img src='/images/template/icons/replie.png' alt='Repliée'/></span>":"" )."
							</div>
						";
						
						if($bool) $this->search( $liste, $niveau+1 );
						
					}
					// Cellule
					else if( isset($value["ID_CELLULE"]) )
						echo "
							<div class='etablissement'>
								<span title='Type ".$value["ID_TYPE"]."' class='icone'><img src='/images/template/types/b/icone-type-".$value["ID_TYPE"].".png' alt='' /></span>
								<span class='type'>".$value["ID_TYPE"]." -&nbsp;CELL</span>
								<span class='nom-etablissement'><a>".$value["LIBELLE"]."</a></span>
								<span class='ville'>".$value["LIBELLE_COMMUNE"]."</span>
								<span class='avis F'>F</span>
							</div>
						";
						// href='/cellule/index/id/".$value["ID_CELLULE"]."' onclick=\"document.location.href='/cellule/index/id/".$value["ID_CELLULE"]."';\"
					// Etablissement
					else
					{
					
						$search = new Model_DbTable_Search;
						$search	->setBatiment("cellule");
						$search	->in($value["ID_ETABLISSEMENT"]);
						$liste = $search->run();
						
						$bool = (count($liste[0])>0)?true:false;
					
						echo "
							<div class='etablissement ". ( ($bool)?(($niveau==0)?"parent":"sous-parent"):"" ) ." '>
								<span title='Type ".$value["ID_TYPE"]."' class='icone'><img src='/images/template/types/b/icone-type-".$value["ID_TYPE"].".png' alt='' /></span>
								<span class='type'>".$value["ID_TYPE"]." -&nbsp;ERP</span>
								<span class='nom-etablissement'><a href='/etablissement/index/id/".$value["ID_ETABLISSEMENT"]."' onclick=\"document.location.href='/etablissement/index/id/".$value["ID_ETABLISSEMENT"]."';\">".stripslashes($value["LIBELLE"])."</a></span>
								<span class='ville'>".$value["LIBELLE_COMMUNE"]."</span>
								<span class='avis ".$value["LIBELLE_AVIS"][0]."'>".$value["LIBELLE_AVIS"][0]."</span>
								".(( $bool )?"<span class='fleche'><img src='/images/template/icons/replie.png' alt='Repliée' /></span>":"" )."
							</div>
						";
						
						if($bool) $this->search( $liste, $niveau+1 );
					}
				}

				if( $niveau == 0 )
				{

					echo "</div>";

					// Pagination
					if( $input[0] instanceof Zend_Paginator )
					echo "
						<!-- Ici finit la liste -->
						<!-- Ici commence la pagination -->
						<div id='pagination' class='grid_11 pull_5'>
							<p>
								<span>
									".$input[0]."
								</span>
							</p>
						</div>
						<!-- Ici finit la pagination -->
					";
				}
				
				echo "</div>";
				
			}
			else
				echo "<p class='grid_16'><br/><br/><strong>Aucun résultat.</strong></p>";
		}
	}
?>