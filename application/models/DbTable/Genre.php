<?php

    /*
        Genre

        Cette classe sert pour récupérer les genre, et les administrer

    */

    class Model_DbTable_Genre extends Zend_Db_Table_Abstract
    {

        protected $_name="genre"; // Nom de la base
        protected $_primary = "ID_GENRE"; // Clé primaire

        // Donne la liste des genres
        public function getGenre( $id = null )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("genre");

            if ($id != null) {
                $select->where("ID_GENRE = $id");

                return $this->fetchRow($select)->toArray();
            } else

                return $this->fetchAll($select)->toArray();

        }

        public function fetchAllSaufSite()
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("genre")
                ->where("ID_GENRE != 1");

            return $this->fetchAll($select);
        }

    }
