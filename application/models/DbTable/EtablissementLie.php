<?php
    class Model_DbTable_EtablissementLie extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissementlie";
        protected $_primary = array("ID_ETABLISSEMENT", "ID_FILS_ETABLISSEMENT");
    }
