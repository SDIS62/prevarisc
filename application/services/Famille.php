<?php

class Service_Famille
{
    /**
     * Récupération de l'ensemble des familles
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_famille = new Model_DbTable_Famille;
    	return $DB_famille->fetchAllPK();
    }
}
