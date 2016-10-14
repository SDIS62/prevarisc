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
                        ->where("ID_DOSSIER = ?", $idDossier);

                return $this->getAdapter()->fetchAll($select);
        }
    }
