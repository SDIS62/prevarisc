<?php

class Model_DbTable_GroupementPreventionniste extends Zend_Db_Table_Abstract
{
    protected $_name="groupementpreventionniste"; // Nom de la base
    protected $_primary = array("ID_GROUPEMENT", "DATEDEBUT_GROUPEMENTPREVENTIONNISTE", "ID_UTILISATEUR"); // Clé primaire
    protected $_referenceMap = array(
            "groupement" => array(
                "columns" => "ID_GROUPEMENT",
                "refTableClass" => "Model_DbTable_Groupement",
                "refColumns" => "ID_GROUPEMENT",
                "onDelete" => self::CASCADE
            ),
            "utilisateurinformations" => array(
                "columns" => "ID_UTILISATEUR",
                "refTableClass" => "Model_DbTable_UtilisateurInformations",
                "refColumns" => "ID_UTILISATEUR"
            )
        );
}
