<?php
    class Model_DbTable_DossierContact extends Zend_Db_Table_Abstract
    {
        protected $_name="dossiercontact"; // Nom de la base
        protected $_primary = array("ID_DOSSIER","ID_UTILISATEURINFORMATIONS"); // Clé primaire
    }
