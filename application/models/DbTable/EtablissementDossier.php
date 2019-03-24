<?php
    class Model_DbTable_EtablissementDossier extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissementdossier";
        protected $_primary = "ID_ETABLISSEMENTDOSSIER";

        public function getEtablissementListe($idDossier)
        {
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('ed' => 'etablissementdossier'))
                    ->joinLeftUsing(array('e' => 'etablissement'), 'ID_ETABLISSEMENT')
                    ->where("ID_DOSSIER = ?", $idDossier)
                    ->where("e.DATESUPPRESSION_ETABLISSEMENT IS NULL");

            return $this->getAdapter()->fetchAll($select);
        }
    }
