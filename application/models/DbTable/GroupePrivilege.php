<?php

class Model_DbTable_GroupePrivilege extends Zend_Db_Table_Abstract
{
    protected $_name = "groupe-privileges";
    
    protected $_referenceMap    = array(
        'Groupe' => array(
            'columns'           => array('ID_GROUPE'),
            'refTableClass'     => 'Model_DbTable_Groupe',
            'refColumns'        => array('ID_GROUPE')
        ),
        'Privilege' => array(
            'columns'           => array('id_privilege'),
            'refTableClass'     => 'Model_DbTable_Privilege',
            'refColumns'        => array('id_privilege')
        )
    );
}