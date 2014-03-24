<?php

class Service_Classe
{
    /**
     * Récupération de l'ensemble des classes
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_classe = new Model_DbTable_Classe; 
    	return $DB_classe->fetchAllPK();
    }
}
