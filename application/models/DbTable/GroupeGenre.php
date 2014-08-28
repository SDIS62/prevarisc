<?php

    class Model_DbTable_GroupeGenre extends Zend_Db_Table_Abstract
    {
        protected $_name="groupegenre";
        protected $_primary = array("ID_GENRE", "ID_GROUPE");

    }
