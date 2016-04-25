<?php
    class Model_DbTable_AdresseCommune extends Zend_Db_Table_Abstract
    {
        protected $_name="adressecommune"; // Nom de la base
        protected $_primary = "NUMINSEE_COMMUNE"; // Cl� primaire

        public function get($q)
        {
            $select = $this->select()->setIntegrityCheck(false);

            $select->from("adressecommune")
                   ->where("LIBELLE_COMMUNE LIKE ?", "%".$q."%")
                   ->order('LENGTH(LIBELLE_COMMUNE)');

            return $this->fetchAll($select)->toArray();
        }

    }
