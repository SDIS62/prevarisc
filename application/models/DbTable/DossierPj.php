<?php

class Model_DbTable_DossierPj extends Zend_Db_Table_Abstract
{

    protected $_name="dossierpj"; // Nom de la base
    protected $_primary = array("ID_DOSSIER","ID_PIECEJOINTE");// Cl� primaire

}
