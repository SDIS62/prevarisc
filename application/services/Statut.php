<?php

class Service_Statut
{
    /**
     * Récupération de l'ensemble des statuts
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_statut = new Model_DbTable_Statut;
    	return $DB_statut->fetchAll()->toArray();
    }
}
