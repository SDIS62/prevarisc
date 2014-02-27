<?php

class Service_Categorie
{
    /**
     * Récupération de l'ensemble des catégories
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_categorie = new Model_DbTable_Categorie;
    	return $DB_categorie->fetchAllPK();
    }
}
