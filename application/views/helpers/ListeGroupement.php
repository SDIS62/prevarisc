<?php

    class Zend_View_Helper_ListeGroupement extends Zend_View_Helper_HtmlElement
    {
        public function listeGroupement($selected, $attribs = null)
        {
            // Modèles
            $model_groupements = new Model_DbTable_Groupement;
            $model_groupementstypes = new Model_DbTable_GroupementType;

            // Liste des types de groupement
            $array_groupementstypes = $model_groupementstypes->fetchAll()->toArray();

            // Initialisation du tableau des groupements
            $array_groupements = array();

            // Pour chaque type, on retouve les model_groupements
            foreach ($array_groupementstypes as $value) {

                $array_groupements[ $value["ID_GROUPEMENTTYPE"] ] = array(
                    0 => $value["LIBELLE_GROUPEMENTTYPE"],
                    1 => $model_groupements->fetchAll("ID_GROUPEMENTTYPE = ".$value["ID_GROUPEMENTTYPE"])->toArray()
                );
            }

            // Attributs
            if ($attribs) {
                $attribs = $this->_htmlAttribs($attribs);
            } else {
                $attribs = '';
            }

            // Affichage
            echo "<select $attribs>";

            foreach ($array_groupements as $key => $groupements) {
                echo "<optgroup id='gpt_$key' label='".$groupements[0]."'>";
                foreach( $groupements[1] as $groupement)
                    echo "<option value='".$groupement["ID_GROUPEMENT"]."' ". ( ($groupement["ID_GROUPEMENT"] == $selected) ? "selected" : "" ) .">".$groupement["LIBELLE_GROUPEMENT"]."</option>";
                echo "</optgroup>";
            }
            echo "</select>";
        }

    }
