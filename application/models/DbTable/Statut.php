<?php

    /*
        Statut

        Cette classe sert pour récupérer les Statuts, et les administrer

    */

    class Model_DbTable_Statut extends Zend_Db_Table_Abstract
    {

        protected $_name="statut"; // Nom de la base
        protected $_primary = "ID_STATUT"; // Clé primaire

        // Donne la liste des catégories
        public function getStatuts( $id = null )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("statut");

            if ($id != null) {
                $select->where("ID_STATUT = $id");

                return $this->fetchRow($select)->toArray();
            } else

                return $this->fetchAll($select)->toArray();

        }

    }
