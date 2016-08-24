<?php

    /*
        Statut

        Cette classe sert pour r�cup�rer les Statuts, et les administrer

    */

    class Model_DbTable_Statut extends Zend_Db_Table_Abstract
    {

        protected $_name="statut"; // Nom de la base
        protected $_primary = "ID_STATUT"; // Cl� primaire

    }
