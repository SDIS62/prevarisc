<?php

class Model_DbTable_DossierDocUrba extends Zend_Db_Table_Abstract
{
    protected $_name="dossierdocurba"; // Nom de la base
    protected $_primary = "ID_DOCURBA"; // Clé primaire

    //prend en parametre un type et retourne toutes les natures associées à ce dossier
    public function getDossierDocUrba($idDossier)
    {
        $select = "SELECT *
            FROM dossierdocurba
            WHERE ID_DOSSIER = '".$idDossier."'
        ;";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

}
