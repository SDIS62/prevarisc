<?php

class Model_DbTable_CommissionTypeEvenement extends Zend_Db_Table_Abstract
{
    protected $_name = "commissiontypeevenement"; // Nom de la base
    protected $_primary = "ID_COMMISSIONTYPEEVENEMENT"; // Cl� primaire

    //Fonction qui r�cup�re toutes les infos g�n�rales d'un dossier
    public function getCommListe()
    {
        $select = "SELECT *
            FROM ".$this->_name.";
        ";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

}
