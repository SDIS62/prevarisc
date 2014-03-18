<?php

class Service_User
{
    /**
     * Récupération des groupes d'utilisateurs
     *
     * @return array
     */
    public function getAllGroupes()
    {
        $DB_groupe = new Model_DbTable_Groupe;
        return $DB_groupe->fetchAll()->toArray();
    }

    /**
     * Récupération de toutes les fonctions des utilisateurs
     *
     * @return array
     */
    public function getAllFonctions()
    {
        $DB_fonction = new Model_DbTable_Fonction();
        return $DB_fonction->fetchAll()->toArray();
    }
}
