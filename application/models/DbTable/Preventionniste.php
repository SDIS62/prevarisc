<?php

class Model_DbTable_Preventionniste extends Zend_Db_Table_Abstract
{

    protected $_name="utilisateur"; // Nom de la base
    protected $_primary = "ID_UTILISATEUR"; // Clé primaire

    public function getPrev($commune, $id_pere)
    {
        $array_result = array();

        // On vérfie si l'établissement père à des prev
        if ($id_pere != "") {
            $search = new Model_DbTable_Search;
            $prev_du_pere = $search->setItem("utilisateur")->setCriteria("etablissementinformations.ID_ETABLISSEMENT", $id_pere)->run();
            if (count($prev_du_pere) > 0) {
                foreach ($prev_du_pere as $uid) {
                    $array_tmp = array();
                    $array_tmp['uid'] = $uid['uid'];
                    $array_tmp['nom'] = $uid['NOM_UTILISATEURINFORMATIONS'];
                    $array_tmp['prenom'] = $uid['PRENOM_UTILISATEURINFORMATIONS'];
                    $array_result[] = $array_tmp;
                }

                return $array_result;
            }
        }

        // On check les prev du groupement
        if ($commune != "") {
            $model_gpt = new Model_DbTable_Groupement;
            $gpt = $model_gpt->getGroupementParVille($commune);
            $prev_des_gpts = $model_gpt->getPreventionnistesByGpt($gpt);
            if (count($prev_des_gpts) > 0) {
                foreach ($prev_des_gpts as $prev_des_gpt) {
                    foreach ($prev_des_gpt as $uid) {
                        $array_tmp = array();
                        $array_tmp['uid'] = $uid['ID_UTILISATEUR'];
                        $array_tmp['nom'] = $uid['NOM_UTILISATEURINFORMATIONS'];
                        $array_tmp['prenom'] = $uid['PRENOM_UTILISATEURINFORMATIONS'];

                        if(in_array($array_tmp, $array_result)) {
                            continue;
                        }

                        $array_result[] = $array_tmp;
                    }
                }
            }

            return $array_result;
        }

        return null;
    }
}
