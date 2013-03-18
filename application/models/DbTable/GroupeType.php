<?php

    class Model_DbTable_GroupeType extends Zend_Db_Table_Abstract
    {
        protected $_name="groupetype";
        protected $_primary = array("ID_TYPE", "ID_GROUPE");

    }
