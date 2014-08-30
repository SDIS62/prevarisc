<?php

class Model_DbTable_Groupe extends Zend_Db_Table_Abstract
{
    protected $_name="groupe"; // Nom de la base
    protected $_primary = "ID_GROUPE"; // Clé primaire
    
    protected $_dependentTables = array('Model_DbTable_GroupePrivilege');
    
    public function delete($id_groupe)
    {
        parent::delete("ID_GROUPE = " . $id_groupe);
    }
}