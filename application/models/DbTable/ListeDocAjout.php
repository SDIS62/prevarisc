<?php

class Model_DbTable_ListeDocAjout extends Zend_Db_Table_Abstract
{
    protected $_name="listedocajout"; // Nom de la base
    protected $_primary = "ID_DOCAJOUT"; // Clé primaire

    //récupere les éventuels documents qui auraient été ajoutés
    public function getDocAjout($id_dossier)
    {
        $select = "SELECT *
        FROM listedocajout
        WHERE id_dossier = '".$id_dossier."'
        ORDER BY ID_DOCAJOUT;";

        return $this->getAdapter()->fetchAll($select);
    }

}
