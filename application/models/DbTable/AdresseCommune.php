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

        public function getMairieInformation($numinsee)
        {
            $select = "SELECT * "
                    . "FROM adressecommune as commune "
                    . "INNER JOIN utilisateurinformations as user "
                    . "ON commune.ID_UTILISATEURINFORMATIONS = "
                    . "user.ID_UTILISATEURINFORMATIONS "
                    . "WHERE commune.NUMINSEE_COMMUNE = '" . $numinsee . "'";

            $result = $this->getAdapter()->fetchAll($select);
            if (count($result) > 0) {
                $result = $result[0];
            } else {
                $result = null;
            }

            return $result;
        }

    }
