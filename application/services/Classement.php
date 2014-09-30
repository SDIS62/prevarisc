<?php

class Service_Classement
{
    /**
     * Récupération de l'ensemble des classes
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_classement = new Model_DbTable_Classement; 
    	return $DB_classement->fetchAllPK();
    }
}
