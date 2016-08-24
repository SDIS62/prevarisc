<?php

class Model_DbTable_PrescriptionRegl extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptionregl"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTIONREGL"; // Cl� primaire

	public function recupPrescRegl($type,$mode = null)
	{
		//retourne la liste des cat�gories de prescriptions par ordre
		if($type == 'etude'){
			$typePresc = 1;
		}else if($type == 'visite'){
			$typePresc = 2;
		}

        $select = $this->select()
			->setIntegrityCheck(false)
			->from(array('pre' => 'prescriptionregl'))
			->where('pre.PRESCRIPTIONREGL_TYPE = ?',$typePresc);

		if($mode != null)
			$select->where('pre.PRESCRIPTIONREGL_VISIBLE = 1');

		return $this->getAdapter()->fetchAll($select);
	}
}
