<?php

class Service_Type
{
    /**
     * Récupération de l'ensemble des types d'activités 
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_type = new Model_DbTable_Type;
    	return $DB_type->fetchAll()->toArray();
    }
}
