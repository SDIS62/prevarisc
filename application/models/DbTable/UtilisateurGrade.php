<?php
    class Model_DbTable_UtilisateurGrade extends Zend_Db_Table_Abstract
    {
        protected $_name="utilisateurgrade";
        protected $_primary = array("ID_UTILISATEUR", "ID_GRADE");
    }
