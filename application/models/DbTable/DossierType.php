<?php

class Model_DbTable_DossierType extends Zend_Db_Table_Abstract
{
    protected $_name="dossiertype"; // Nom de la base
    protected $_primary = "ID_DOSSIERTYPE"; // Clé primaire

    public function getDossierType()
    {
        $select = "SELECT *
            FROM dossiertype
        ;";

        return $this->getAdapter()->fetchAll($select);
    }

}
