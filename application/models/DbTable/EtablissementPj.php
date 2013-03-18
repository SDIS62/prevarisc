<?php

class Model_DbTable_EtablissementPj extends Zend_Db_Table_Abstract
{

    protected $_name="etablissementpj"; // Nom de la base
    protected $_primary = array("ID_ETABLISSEMENT","ID_PIECEJOINTE");// Clé primaire

}
