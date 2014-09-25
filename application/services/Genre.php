<?php

class Service_Genre
{
    /**
     * Récupération de l'ensemble des genres
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_genre = new Model_DbTable_Genre;
    	return $DB_genre->fetchAll()->toArray();
    }
    
    public function getClassements()
    {
        $DB_classement = new Model_DbTable_Classement;
    	return $DB_classement->fetchAll()->toArray();
    }
}
