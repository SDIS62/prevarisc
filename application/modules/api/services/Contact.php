<?php

class Api_Service_Contact
{
    /**
     * Recherche des contacts
     *
     * @param string $name
     * @return string
     */
    public function get($name)
    {
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        return $DB_informations->getAllContacts($name);
    }
}