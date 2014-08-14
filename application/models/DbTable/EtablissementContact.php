<?php
    class Model_DbTable_EtablissementContact extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissementcontact"; // Nom de la base
        protected $_primary = array("ID_ETABLISSEMENT", "ID_UTILISATEURINFORMATIONS"); // Clé primaire
    }
