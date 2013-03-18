<?php
    class Model_DbTable_EtablissementInformationsPreventionniste extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissementinformationspreventionniste"; // Nom de la base
        protected $_primary = array("ID_ETABLISSEMENTINFORMATIONS", "ID_UTILISATEUR"); // Clé primaire
    }
