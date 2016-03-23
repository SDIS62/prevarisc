<?php

class Model_DbTable_Changement extends Zend_Db_Table_Abstract
{

    protected $_name="changement";
    protected $_primary = "ID_CHANGEMENT";

    /**
     * [findAll description]
     * @return [type] [description]
     */
    public function findAll()
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("changement");
        
        return $this->fetchAll($select)->toArray();
    }
}
