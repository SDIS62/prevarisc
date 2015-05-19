<?php
    class Model_DbTable_DossierLie extends Zend_Db_Table_Abstract
    {
        protected $_name="dossierlie"; // Nom de la base
        protected $_primary = array("ID_DOSSIERLIE"); // Clé primaire

        public function getDossierLie( $idDossier)
        {
            $select = "
                SELECT *
                FROM dossierlie
                WHERE (ID_DOSSIER1 = '".$idDossier."' OR ID_DOSSIER2 = '".$idDossier."');
            ";

            //echo  $select;
            return $this->getAdapter()->fetchAll($select);
        }
    }
