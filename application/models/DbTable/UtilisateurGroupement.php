<?php
    class Model_DbTable_UtilisateurGroupement extends Zend_Db_Table_Abstract
    {
        protected $_name="utilisateurgroupement";
        protected $_primary = array("ID_UTILISATEUR", "ID_GROUPEMENT");
    }
