<?php
    class Model_DbTable_CommissionContact extends Zend_Db_Table_Abstract
    {
        protected $_name="commissioncontact"; // Nom de la base
        protected $_primary = array("ID_COMMISSION", "ID_UTILISATEURINFORMATIONS"); // Clé primaire
    }
