<?php

    /*
        Genre

        Cette classe sert pour r�cup�rer les genre, et les administrer

    */

    class Model_DbTable_Genre extends Zend_Db_Table_Abstract
    {

        protected $_name="genre"; // Nom de la base
        protected $_primary = "ID_GENRE"; // Cl� primaire

    }
