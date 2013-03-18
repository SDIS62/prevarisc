<?php

    class Model_DbTable_PrescriptionLibelle extends Zend_Db_Table_Abstract
    {
        protected $_name="prescriptionlibelle"; // Nom de la base
        protected $_primary = "ID_PRESCRIPTIONLIBELLE"; // Clé primaire

        //Fonction qui récupère toutes les infos générales d'un dossier
        /*
        public function getGeneral($id)
        {
            $select = "SELECT *
            FROM dossier, dossiertype, dossiernature, commission, commissiontype
            WHERE dossier.commission_dossier =	commission.id_commission
            AND commission.id_commissiontype = commissiontype.id_commissiontype
            AND dossier.type_dossier = dossiertype.id_dossiertype
            AND dossier.nature_dossier = dossiernature.id_dossiernature
            AND dossier.id_dossier = '".$id."';";
            //echo $select;
            return $this->getAdapter()->fetchRow($select);
        }
        */
    }
