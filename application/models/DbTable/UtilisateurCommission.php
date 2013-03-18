<?php
    class Model_DbTable_UtilisateurCommission extends Zend_Db_Table_Abstract
    {
        protected $_name="utilisateurcommission";
        protected $_primary = array("ID_UTILISATEUR", "ID_COMMISSION");
    }
