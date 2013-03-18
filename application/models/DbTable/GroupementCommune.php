<?php

class Model_DbTable_GroupementCommune extends Zend_Db_Table_Abstract
{
    protected $_name="groupementcommune"; // Nom de la base
    protected $_primary = "ID_GROUPEMENT"; // Clé primaire
    protected $_referenceMap = array(
                "groupement" => array(
                    "columns" => "ID_GROUPEMENT",
                    "refTableClass" => "Model_DbTable_Groupement",
                    "refColumns" => "ID_GROUPEMENT",
                    "onDelete" => self::CASCADE
                ),
                "utilisateur" => array(
                    "columns" => "NUMINSEE_COMMUNE",
                    "refTableClass" => "Model_DbTable_AdresseCommune",
                    "refColumns" => "NUMINSEE_COMMUNE"
                )
        );
}
