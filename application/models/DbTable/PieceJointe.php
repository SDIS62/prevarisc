<?php

    class Model_DbTable_PieceJointe extends Zend_Db_Table_Abstract
    {
        protected $_name="piecejointe"; // Nom de la base
        protected $_primary = "ID_PIECEJOINTE"; // Clé primaire

        public function affichagePieceJointe($table, $champ, $identifiant)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("piecejointe")
                ->join($table, "piecejointe.ID_PIECEJOINTE = $table.ID_PIECEJOINTE")
                ->where($champ." = ".$identifiant)
                ->order("piecejointe.ID_PIECEJOINTE DESC");

            return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
        }

        public function maxPieceJointe()
        {
            //echo "les champs : ".$table.$champ.$identifiant."<br/>";
            $select = "SELECT MAX(ID_PIECEJOINTE)
            FROM piecejointe
            ;";
            //echo $select;
            return $this->getAdapter()->fetchRow($select);
        }
    }
