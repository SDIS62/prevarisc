<?php

	class Zend_View_Helper_AfficheDoc
	{
		public function afficheDoc($natureId,$id,$libelle,$ref=null,$date=null,$type=null){
			if(!$date){
				//document n'ayant PAS d'enregistrement dans la BD
				$styleInput = "display:none;";
				$etatCheck = "";
				$styleChecked = "";
				$styleEdit = "display:none;";
				$styleValid = "";
				$styleDate = "";
			}else{
				//document ayant un enregistrement dans la BD
				$dateTab = explode("-",$date);
				$date = $dateTab[2]."/".$dateTab[1]."/".$dateTab[0];
				
				$styleInput = "";
				$etatCheck = "disabled='disabled'";
				$styleChecked = "checked='checked'";
				$styleEdit = "";
				$styleValid = "display:none;";
				$styleDate = "disabled='disabled'";
			}
			return "
				<li class='divDoc' name='divDoc' id='".$natureId."_".$id.$type."' style='display: block; height: 25px;margin: 15px;'>
					<div style='float:left;'>
						<input type='checkbox' ".$styleChecked." ".$etatCheck." name='check_".$natureId."_".$id.$type."' id='check_".$natureId."_".$id.$type."' />
					</div>
					<div class='grid_8 alpha libelle'  style='padding-left:10px;' >
						<strong>".nl2br($libelle)."</strong>
					</div>
					<div id='div_input_".$natureId."_".$id.$type."' style='".$styleInput."'>
						<div class='grid_3'>
							<input type='text' readonly='true' name='ref_".$natureId."_".$id.$type."' id='ref_".$natureId."_".$id.$type."' value=\"".$ref."\" style='width: 100%;' />
						</div>
						<div class='grid_2'>
							<input type='text' readonly='true' ".$styleDate."  class='date' name='date_".$natureId."_".$id.$type."' id='date_".$natureId."_".$id.$type."' value='".$date."' />
						</div>
						<div class='grid_2 omega'>
							<span class='modif' id='modif_".$natureId."_".$id.$type."' style='' >
									<button class='editDoc'>Edition</button>
							</span>
							<span id='valid_".$natureId."_".$id.$type."' style='".$styleValid."'>
									<button class='validDoc'>Valider</button>
									<button class='cancelDoc'>Annuler</button>
									<button class='deleteDoc' name='".$natureId."_".$id.$type."'>Supprimer</button>
								</a>
							</span>
						</div>	
					</div>
					<br class='clear'/>
				</li>
			";
		}
		
	}

?>