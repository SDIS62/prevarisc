<?php

class Service_Commission
{
    /**
     * Récupération de l'ensemble des commissions
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_commission = new Model_DbTable_Commission; 
    	return $DB_commission->fetchAllPK();
    }
}
