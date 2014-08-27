<?php

class Model_DbTable_Resource extends Zend_Db_Table_Abstract
{
    protected $_name = "resources";
    
    protected $_referenceMap    = array(
        'Privilege' => array(
            'columns'           => array('id_privilege'),
            'refTableClass'     => 'Model_DbTable_Privilege',
            'refColumns'        => array('id_privilege')
        )
    );
    
    protected $_dependentTables = array('Model_DbTable_Privilege');
}