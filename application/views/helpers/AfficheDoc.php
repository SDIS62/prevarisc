<?php

    class View_Helper_AfficheDoc
    {
        public function afficheDoc($verrou,$natureId,$id,$libelle,$ref=null,$date=null,$type=null)
        {
            if (!$date) {
                //document n'ayant PAS d'enregistrement dans la BD
                $styleInput = "display:none;";
                $etatCheck = "";
                $styleChecked = "";
                $styleEdit = "display:none;";
                $styleValid = "";
                $styleDate = "";
            } else {
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
			
			if($date == "00/00/0000")
				$date = "";

            return "
                <li class='divDoc row-fluid span12' name='divDoc' id='".$natureId."_".$id.$type."' style='display: block; margin: 0 15px 15px 15px;'>
                    <div style='float:left;' class='span1'>
                        <input type='checkbox' ".$styleChecked." ".$etatCheck." name='check_".$natureId."_".$id.$type."' id='check_".$natureId."_".$id.$type."' ".( ( $verrou == 1)?"disabled='disabled'":"" )." />
                    </div>
                    <div class='span4 libelle' >
                        <strong>".nl2br($libelle)."</strong>
                    </div>
                    <div id='div_input_".$natureId."_".$id.$type."' class='span7' style='".$styleInput."'>
                        <div class='span4'>
                            <input type='text' readonly='true' name='ref_".$natureId."_".$id.$type."' id='ref_".$natureId."_".$id.$type."' value=\"".$ref."\" style='width: 100%;' />
                        </div>
                        <div class='span2'>
                            <input type='text' readonly='true' ".$styleDate."  class='date' name='date_".$natureId."_".$id.$type."' id='date_".$natureId."_".$id.$type."' value='".$date."' />
                        </div>
                        <div class='span3'>
                            <span class='modif' id='modif_".$natureId."_".$id.$type."' style='".( ( $verrou == 1)?"display:none;":"")."' >
                                    <button class='editDoc btn'><i class='icon-pencil'></i>&nbsp;</button>
                            </span>
                            <span id='valid_".$natureId."_".$id.$type."' style='".$styleValid."'>
                                    <button class='validDoc btn'><i class='icon-ok'></i>&nbsp;</button>
                                    <button class='cancelDoc btn'><i class='icon-remove'></i>&nbsp;</button>
                                    <button class='deleteDoc btn' name='".$natureId."_".$id.$type."'><i class='icon-trash'></i>&nbsp;</button>
                                </a>
                            </span>
                        </div>
                    </div>
                    <br class='clear'/>
                </li>
				<br class='clear'/>
            ";
        }

    }
