<?php

class Model_DbTable_Avis extends Zend_Db_Table_Abstract
{

    protected $_name="avis"; // Nom de la base
    protected $_primary = "ID_AVIS"; // Cl� primaire

    //Fonction qui r�cup�re tous les avis existant pour cr�er un select par exemple
    public function getAvis($tousLesChamps = 1)
    {
        if ($tousLesChamps == 1) {
            $select = "SELECT *
                FROM avis
            ;";
        }
        else {
            $select = "SELECT *
                FROM avis
                WHERE VISIBLE_DOSSIER = 0;";
        }
        

        return $this->getAdapter()->fetchAll($select);
    }

    public function getAvisLibelle($idAvis, $tousLesChamps = 1)
    {
        if ($tousLesChamps == 1) 
        {
            $select = "SELECT *
                FROM avis
                WHERE ID_AVIS = '".$idAvis."'
            ;";
        }
        else {
            $select = "SELECT *
                FROM avis
                WHERE ID_AVIS = '".$idAvis."'
                    AND VISIBLE_DOSSIER = 0;";
        }

        return $this->getAdapter()->fetchRow($select);
    }

}
