<?php

    class Model_DbTable_EtsTextesAppl extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissementtextapp"; // Nom de la base

        public function recupTextes($id_etablissement)
		{
            return $this->fetchAll("ID_ETABLISSEMENT = " . $id_etablissement);
		}

    }
