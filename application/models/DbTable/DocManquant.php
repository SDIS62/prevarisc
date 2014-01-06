<?php

class Model_DbTable_DocManquant extends Zend_Db_Table_Abstract
{

    protected $_name="docmanquant"; // Nom de la base
    protected $_primary = "ID_DOCMANQUANT";// Clé primaire
	
	public function getDocManquant()
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('dm' => 'docmanquant'));
			 
		return $this->getAdapter()->fetchAll($select);	

	}
	
}
