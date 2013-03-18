<?php
    class Model_DbTable_DossierLie extends Zend_Db_Table_Abstract
    {
        protected $_name="dossierlie"; // Nom de la base
        protected $_primary = array("ID_DOSSIERLIE"); // Clé primaire

        public function getDossierLie( $idDossier)
        {
            /*
            $select = "SELECT *
                FROM dossierlie, dossier, dossiertype, dossiernature, dossiernatureliste, etablissementdossier, etablissementinformations
                WHERE dossier.TYPE_DOSSIER = dossiertype.ID_DOSSIERTYPE
                AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                AND dossiernature.ID_NATURE = dossiernatureliste.ID_DOSSIERNATURE
                AND etablissementdossier.ID_DOSSIER = dossier.ID_DOSSIER
                AND etablissementinformations.ID_ETABLISSEMENT = etablissementdossier.ID_ETABLISSEMENT
                AND dossier.ID_DOSSIER != ".$idDossier."
                AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = (select max(DATE_ETABLISSEMENTINFORMATIONS) from etablissementinformations where ID_ETABLISSEMENT = etablissementdossier.ID_ETABLISSEMENT )
                GROUP BY ID_DOSSIERLIE
            ";
            */
            $select = "
                SELECT *
                FROM dossierlie
                WHERE (ID_DOSSIER1 = '".$idDossier."' OR ID_DOSSIER2 = '".$idDossier."');
            ";

            //echo  $select;
            return $this->getAdapter()->fetchAll($select);
        }
    }
