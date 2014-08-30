<?php

class Model_DbTable_Privilege extends Zend_Db_Table_Abstract
{
    protected $_name = "privileges";
    
    protected $_referenceMap    = array(
        'Privilege' => array(
            'columns'           => array('id_resource'),
            'refTableClass'     => 'Model_DbTable_Resource',
            'refColumns'        => array('id_resource')
        )
    );
    
    protected $_dependentTables = array('Model_DbTable_GroupePrivilege', 'Model_DbTable_Resource');
}